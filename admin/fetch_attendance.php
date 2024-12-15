<?php
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    // Handle GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get the date from the request, default to today if not provided
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get the date from the request, default to today if not provided
            $date = $_GET['date'] ?? date('Y-m-d');
            $teacherId = $_GET['teacher_id'] ?? null;
    
            // Comprehensive query to fetch attendance with student details
            $query = "
                SELECT 
                    a.id AS attendance_record_id,
                    a.student_id, 
                    a.attendance_date,
                    a.attendance,
                    s.lrn, 
                    s.full_name, 
                    s.sex, 
                    s.grade, 
                    s.section,
                    t.id AS teacher_id,
                    t.full_name AS teacher_name
                FROM 
                    attendance a
                JOIN 
                    students s ON a.student_id = s.id
                LEFT JOIN 
                    teachers t ON a.teacher_id = t.id
                WHERE 
                    a.attendance_date = :attendance_date
            ";
    
            // Prepare parameters
            $params = [':attendance_date' => $date];
    
            // Add teacher filter if provided
            if ($teacherId) {
                $query .= " AND a.teacher_id = :teacher_id";
                $params[':teacher_id'] = $teacherId;
            }
    
            $query .= " ORDER BY s.full_name";
    
            // Prepare and execute query
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // If no records found, return an empty array instead of throwing an error
            echo json_encode([
                'status' => 'success',
                'attendance' => $attendanceRecords,
                'message' => $attendanceRecords ? 'Records found' : 'No records found'
            ]);
        }
    }
    // Handle POST request for updating attendance
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentId = $_POST['student_id'] ?? null;
        $attendanceStatus = $_POST['attendance'] ?? null;
        $attendanceDate = $_POST['attendance_date'] ?? date('Y-m-d'); 

        // Ensure that student_id and attendance are provided
        if ($studentId && $attendanceStatus) {
            // Check if the student exists in the database
            $checkStudentQuery = "SELECT COUNT(*) FROM students WHERE id = :student_id";
            $checkStmt = $pdo->prepare($checkStudentQuery);
            $checkStmt->execute([':student_id' => $studentId]);
            $studentExists = $checkStmt->fetchColumn();

            // If student exists, update attendance
            if ($studentExists) {
                // Check if the attendance record already exists for the student on the given date
                $checkAttendanceQuery = "SELECT COUNT(*) FROM attendance WHERE student_id = :student_id AND attendance_date = :attendance_date";
                $checkAttendanceStmt = $pdo->prepare($checkAttendanceQuery);
                $checkAttendanceStmt->execute([
                    ':student_id' => $studentId,
                    ':attendance_date' => $attendanceDate
                ]);
                $attendanceExists = $checkAttendanceStmt->fetchColumn();

                // If attendance record exists, update it
                if ($attendanceExists) {
                    $updateQuery = "
                        UPDATE attendance 
                        SET attendance = :attendance 
                        WHERE student_id = :student_id AND attendance_date = :attendance_date
                    ";

                    $query = $pdo->prepare($updateQuery);
                    $result = $query->execute([
                        ':student_id' => $studentId,
                        ':attendance_date' => $attendanceDate,
                        ':attendance' => $attendanceStatus
                    ]);

                    // Check if the update was successful
                    if ($result) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Attendance updated successfully.'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Failed to update attendance.',
                            'debug' => $query->errorInfo()
                        ]);
                    }
                } else {
                    // If no attendance record exists for the given date, insert a new record
                    $insertQuery = "
                        INSERT INTO attendance (student_id, attendance_date, attendance)
                        VALUES (:student_id, :attendance_date, :attendance)
                    ";

                    $query = $pdo->prepare($insertQuery);
                    $result = $query->execute([
                        ':student_id' => $studentId,
                        ':attendance_date' => $attendanceDate,
                        ':attendance' => $attendanceStatus
                    ]);

                    // Check if the insert was successful
                    if ($result) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Attendance recorded successfully.'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Failed to record attendance.',
                            'debug' => $query->errorInfo()
                        ]);
                    }
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Student ID does not exist.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid input data.'
            ]);
        }
    } 
    // Handle invalid request method
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
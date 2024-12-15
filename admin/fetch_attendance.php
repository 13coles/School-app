<?php
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    // Handle GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch attendance records with an INNER JOIN
        $query = "
            SELECT 
                a.student_id, 
                s.lrn, 
                s.full_name, 
                s.sex, 
                s.grade, 
                s.section, 
                a.attendance, 
                a.attendance_date 
            FROM 
                students s
            INNER JOIN 
                attendance a ON s.id = a.student_id
        ";

        $query = $pdo->prepare($query);
        $query->execute();
        $attendanceRecords = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'attendance' => $attendanceRecords
        ]);
    } 
    // Handle POST request
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentId = $_POST['student_id'] ?? null;
        $attendanceStatus = $_POST['attendance'] ?? null;
        $attendanceDate = $_POST['attendance_date'] ?? date('Y-m-d'); // Default to today if no date is provided

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

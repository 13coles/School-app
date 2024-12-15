<?php
session_start(); 
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['teacher_section'])) {
        throw new Exception("Teacher section not set in session.");
    }

    $teacher_section = $_SESSION['teacher_section']; 

    # Handle GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        # Fetch attendance records for the teacher's section with an INNER JOIN
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
            WHERE 
                s.section = :section
            ORDER BY 
                a.attendance_date DESC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([':section' => $teacher_section]);
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'attendance' => $attendanceRecords
        ]);
    } 

    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentId = $_POST['student_id'] ?? null;
        $attendanceStatus = $_POST['attendance'] ?? null;
        $attendanceDate = $_POST['attendance_date'] ?? date('Y-m-d'); 

        # Ensure that student_id and attendance are provided
        if ($studentId && $attendanceStatus) {
            # Check if the student exists and belongs to the teacher's section
            $checkStudentQuery = "
                SELECT COUNT(*) 
                FROM students 
                WHERE id = :student_id AND section = :section
            ";
            $checkStmt = $pdo->prepare($checkStudentQuery);
            $checkStmt->execute([
                ':student_id' => $studentId,
                ':section' => $teacher_section
            ]);
            $studentExists = $checkStmt->fetchColumn();

            if ($studentExists) {
                # Check if the attendance record already exists for the student on the given date
                $checkAttendanceQuery = "
                    SELECT COUNT(*) 
                    FROM attendance 
                    WHERE student_id = :student_id AND attendance_date = :attendance_date
                ";
                $checkAttendanceStmt = $pdo->prepare($checkAttendanceQuery);
                $checkAttendanceStmt->execute([
                    ':student_id' => $studentId,
                    ':attendance_date' => $attendanceDate
                ]);
                $attendanceExists = $checkAttendanceStmt->fetchColumn();

                if ($attendanceExists) {
                    # Update existing attendance record
                    $updateQuery = "
                        UPDATE attendance 
                        SET attendance = :attendance 
                        WHERE student_id = :student_id AND attendance_date = :attendance_date
                    ";
                    $stmt = $pdo->prepare($updateQuery);
                    $result = $stmt->execute([
                        ':student_id' => $studentId,
                        ':attendance_date' => $attendanceDate,
                        ':attendance' => $attendanceStatus
                    ]);

                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Attendance updated successfully.'
                    ]);
                } else {
                    # Insert new attendance record
                    $insertQuery = "
                        INSERT INTO attendance (student_id, attendance_date, attendance)
                        VALUES (:student_id, :attendance_date, :attendance)
                    ";
                    $stmt = $pdo->prepare($insertQuery);
                    $result = $stmt->execute([
                        ':student_id' => $studentId,
                        ':attendance_date' => $attendanceDate,
                        ':attendance' => $attendanceStatus
                    ]);

                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Attendance recorded successfully.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Student does not belong to your section.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid input data.'
            ]);
        }
    } 
    # Handle invalid request method
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

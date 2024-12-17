<?php
session_start(); 
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    // Handle GET request for fetching attendance
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get filter parameters
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $selectedTeacherId = $_GET['teacher_id'] ?? null;

        // Base query with joins to get student and teacher information
        $query = "
            SELECT 
                a.student_id, 
                s.lrn, 
                s.full_name, 
                s.sex, 
                s.grade, 
                s.section, 
                a.attendance, 
                a.attendance_date,
                a.teacher_id,
                t.full_name as teacher_name
            FROM 
                attendance a
            JOIN 
                students s ON a.student_id = s.id
            LEFT JOIN 
                teachers t ON a.teacher_id = t.id
            WHERE 
                a.attendance_date = :attendance_date
        ";

        // Add teacher filter if provided
        $params = [':attendance_date' => $selectedDate];
        if ($selectedTeacherId) {
            $query .= " AND a.teacher_id = :teacher_id";
            $params[':teacher_id'] = $selectedTeacherId;
        }

        $query .= " ORDER BY a.attendance_date DESC";

        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'attendance' => $attendanceRecords
        ]);
    } 
    // Handle POST request for updating attendance
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate input
        $studentId = $_POST['student_id'] ?? null;
        $attendanceStatus = $_POST['attendance'] ?? null;
        $attendanceDate = $_POST['attendance_date'] ?? date('Y-m-d');
        $teacherId = $_SESSION['teacher_id'] ?? null; 

        // Validate required fields
        if (!$studentId || !$attendanceStatus || !$teacherId) {
            throw new Exception("Missing required parameters");
        }

        // Check if the attendance record exists
        $checkQuery = "
            SELECT COUNT(*) 
            FROM attendance 
            WHERE student_id = :student_id AND attendance_date = :attendance_date
        ";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([
            ':student_id' => $studentId,
            ':attendance_date' => $attendanceDate
        ]);
        $recordExists = $checkStmt->fetchColumn();

        // Prepare the query based on whether the record exists
        if ($recordExists) {
            // Update existing record
            $query = "
                UPDATE attendance 
                SET 
                    attendance = :attendance,
                    teacher_id = :teacher_id
                WHERE 
                    student_id = :student_id AND 
                    attendance_date = :attendance_date
            ";
        } else {
            // Insert new record
            $query = "
                INSERT INTO attendance (
                    student_id, 
                    attendance_date, 
                    attendance, 
                    teacher_id
                ) VALUES (
                    :student_id, 
                    :attendance_date, 
                    :attendance, 
                    :teacher_id
                )
            ";
        }

        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':student_id' => $studentId,
            ':attendance_date' => $attendanceDate,
            ':attendance' => $attendanceStatus,
            ':teacher_id' => $teacherId
        ]);

        // Check execution result
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => $recordExists 
                    ? 'Attendance updated successfully.' 
                    : 'Attendance recorded successfully.'
            ]);
        } else {
            throw new Exception("Failed to save attendance record");
        }
    }
} catch (Exception $e) {
    // Error handling
    error_log("Attendance Error: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
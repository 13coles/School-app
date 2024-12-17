<?php
session_start(); 

require_once './utils/db_connection.php';
require_once './utils/access_control.php';

checkAccess(['student']);

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
        throw new Exception("Unauthorized access. Student login required.");
    }

    if (!isset($_SESSION['id'])) {
        throw new Exception("Student ID not found in session.");
    }

    $student_id = $_SESSION['student_id'];
    $requestedDate = $_GET['date'] ?? date('Y-m-d');

    $query = $pdo->prepare("
        SELECT 
            a.id AS attendance_record_id,
            a.attendance_date,
            a.attendance,
            s.id AS student_id,
            s.lrn, 
            s.full_name, 
            s.birth_date, 
            s.sex, 
            s.grade, 
            s.section, 
            t.full_name AS teacher_name
        FROM 
            students s
        LEFT JOIN 
            attendance a ON a.student_id = s.id
        LEFT JOIN 
            teachers t ON a.teacher_id = t.id
        WHERE 
            s.id = :student_id
        ORDER BY 
            a.attendance_date DESC
    ");
    
    $query->execute(['student_id' => $student_id]);
    
    $attendanceRecords = $query->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'status' => 'success',
        'attendance' => $attendanceRecords,
        'total_count' => count($attendanceRecords),
        'student_id' => $student_id,
        'requested_date' => $requestedDate
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    // Database-specific error handling
    $errorResponse = [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'code' => $e->getCode()
    ];
    
    http_response_code(500);
    echo json_encode($errorResponse);

} catch (Exception $e) {
    // General error handling
    $errorResponse = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    
    http_response_code(403);
    echo json_encode($errorResponse);
}
exit;
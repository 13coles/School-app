<?php
session_start(); // Start the session

require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    // Check if the user is logged in and has a teacher role
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
        throw new Exception("Unauthorized access. Teacher login required.");
    }

    // Ensure teacher_id is set
    if (!isset($_SESSION['id'])) {
        throw new Exception("Teacher ID not found in session.");
    }

    $teacher_id = $_SESSION['teacher_id'];

    $query = $pdo->prepare("
        SELECT 
            s.id, 
            s.lrn, 
            s.full_name, 
            s.birth_date, 
            s.sex, 
            s.grade, 
            s.section, 
            s.learning_modality,
            s.barangay, 
            s.municipality, 
            s.province,
            s.contact_number,
            tsa.id as assignment_id
        FROM 
            teacher_student_assignments tsa
        JOIN 
            students s ON tsa.student_id = s.id
        WHERE 
            tsa.teacher_id = :teacher_id
        ORDER BY 
            s.grade, 
            s.section, 
            s.full_name 
    ");
    
    $query->execute(['teacher_id' => $teacher_id]);
    
    $students = $query->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'status' => 'success',
        'students' => $students,
        'total_count' => count($students),
        'teacher_id' => $teacher_id
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
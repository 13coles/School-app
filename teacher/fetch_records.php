<?php
session_start(); // Start the session

require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    // Check if the session variable exists
    if (!isset($_SESSION['teacher_section'])) {
        throw new Exception("Teacher section not set in session.");
    }

    $teacher_section = $_SESSION['teacher_section']; 

    $query = $pdo->prepare("
        SELECT 
            id, 
            lrn, 
            full_name, 
            birth_date, 
            sex, 
            grade, 
            section, 
            learning_modality,
            barangay, 
            municipality, 
            province,
            contact_number
        FROM students 
        WHERE section = :section
        ORDER BY id DESC
    ");
    
    $query->execute(['section' => $teacher_section]);
    
    $students = $query->fetchAll(PDO::FETCH_ASSOC);

    $processedStudents = array_map(function($student) {
        $student['birth_date'] = date('Y-m-d', strtotime($student['birth_date']));
        return $student;
    }, $students);

    $response = [
        'status' => 'success',
        'students' => $processedStudents,
        'total_count' => count($processedStudents)
    ];

    echo json_encode($response);

} catch (Exception $e) {
    $errorResponse = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString() 
    ];
    
    http_response_code(500);
    echo json_encode($errorResponse);
}
exit;
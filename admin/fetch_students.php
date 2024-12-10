<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    try {
        # prepare the query to fetch all student details
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
            ORDER BY id DESC
        ");
        
        $query->execute();
        
        $students = $query->fetchAll(PDO::FETCH_ASSOC);

        # formatting birthday in a year-month-date format
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
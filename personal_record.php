<?php
    session_start();
    require_once './utils/db_connection.php';

    header('Content-Type: application/json');

    try {
        error_log('PERSONAL RECORD REQUEST - Full Session Data: ' . print_r($_SESSION, true));

        # check if the user is logged in and is a student
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
            error_log('PERSONAL RECORD - Unauthorized access: Not a student or no role set');
            throw new Exception("Unauthorized access");
        }

        # use the student_id from the session
        $student_id = $_SESSION['student_id'];
        error_log("PERSONAL RECORD - Using student_id: $student_id");

        # prepare the SQL query to fetch the student's personal record
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
                u.id as user_id,
                u.username
            FROM students s
            JOIN users u ON s.id = u.student_id
            WHERE s.id = :student_id
        ");
        
        $query->execute([':student_id' => $student_id]);
        
        $student = $query->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            error_log("PERSONAL RECORD - No student record found for ID: $student_id");
            throw new Exception("Student record not found");
        }

        $response = [
            'status' => 'success',
            'students' => [$student],
            'debug' => [
                'session_student_id' => $student_id,
                'query_result_count' => $query->rowCount(),
                'session_data' => $_SESSION
            ]
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        $errorResponse = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'debug' => [
                'session_data' => $_SESSION,
                'student_id' => $_SESSION['student_id'] ?? 'Not set'
            ]
        ];
        
        error_log('PERSONAL RECORD - Error: ' . $e->getMessage());
        
        http_response_code(500);
        echo json_encode($errorResponse);
    }
    exit;
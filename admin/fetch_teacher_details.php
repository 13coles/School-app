<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function getTeacherDetails($teacherId) {
        global $pdo;

        try {
            // Query the teacher record fetching
            $query = $pdo->prepare("
                SELECT * FROM teachers 
                WHERE id = :id
            ");

            $query->execute([':id' => $teacherId]);
            $teacher = $query->fetch(PDO::FETCH_ASSOC);

            if (!$teacher) {
                throw new Exception("Teacher not found");
            }

            echo json_encode([
                'status' => 'success',
                'teacher' => $teacher
            ]);
            exit;

        } catch (Exception $e) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        // Sanitize incoming teacher ID from the request input
        $teacherId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if (empty($teacherId)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid teacher ID'
            ]);
            exit;
        }
    
        getTeacherDetails($teacherId); 
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing teacher ID'
        ]);
        exit;
    }
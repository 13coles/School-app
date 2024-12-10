<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function getStudentDetails($studentId) {
        global $pdo;

        try {
            $query = $pdo->prepare("
                SELECT * FROM students 
                WHERE id = :id
            ");

            $query->execute([':id' => $studentId]);
            $student = $query->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                throw new Exception("Student not found");
            }

            echo json_encode([
                'status' => 'success',
                'student' => $student
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
        $studentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if ($studentId === false) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid student ID'
            ]);
            exit;
        }

        getStudentDetails($studentId);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing student ID'
        ]);
        exit;
    }
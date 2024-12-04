<?php
    session_start();
    require_once './utils/db_connection.php';
    require_once './utils/access_control.php';

    header('Content-Type: application/json');

    function getStudentDetails($studentId) {
        global $pdo;

        try {
            checkAccess(['student']);

            # ensureonly students can access their own details
            if ($_SESSION['student_id'] != $studentId) {
                throw new Exception("Unauthorized access");
            }

            $query = $pdo->prepare("
                SELECT 
                    s.*,  
                    u.username 
                FROM students s
                LEFT JOIN users u ON s.id = u.student_id
                WHERE s.id = :student_id
            ");

            $query->execute([':student_id' => $studentId]);
            
            $student = $query->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                throw new Exception("Student not found");
            }

            $response = [
                'status' => 'success',
                'student' => $student
            ];

            echo json_encode($response);
            exit;

        } catch (Exception $e) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['student_id'])) {
        $studentId = filter_input(INPUT_GET, 'student_id', FILTER_VALIDATE_INT);
        
        if ($studentId === false || $studentId === null) {
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
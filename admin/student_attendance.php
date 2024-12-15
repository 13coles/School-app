<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function createAttendanceRecord($student_id, $attendance_date, $attendance) {
        global $pdo;

        try {
            $query = $pdo->prepare("
                INSERT INTO attendance (
                    student_id, 
                    attendance_date, 
                    attendance
                ) VALUES (
                    :student_id, 
                    :attendance_date, 
                    :attendance
                )
            ");

            $result = $query->execute([
                ':student_id' => $student_id,
                ':attendance_date' => $attendance_date,
                ':attendance' => $attendance,
            ]);

            if (!$result) {
                $errorInfo = $query->errorInfo();
                throw new Exception("Attendance record creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }

            return true;

        } catch (Exception $e) {
            error_log("Attendance record creation error: " . $e->getMessage());
            throw $e;
        }
    }

    function handleAttendanceSubmission($data) {
        global $pdo;
    
        try {
            error_log("Received attendance data: " . print_r($data, true));
    
            $requiredFields = ['student_id', 'attendance_date', 'attendance'];
    
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    error_log("Missing or empty field: $field");
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }
    
            $pdo->beginTransaction();
    
            createAttendanceRecord($data['student_id'], $data['attendance_date'], $data['attendance']);
    
            $pdo->commit();
    
            echo json_encode([
                'status' => 'success',
                'message' => 'Attendance recorded successfully'
            ]);
            exit;
    
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
    
            error_log("Attendance submission error: " . $e->getMessage());
            
            header('HTTP/1.1 400 Bad Request');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handleAttendanceSubmission($_POST);
    }
    exit;
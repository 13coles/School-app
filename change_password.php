<?php
    session_start();

    require_once('./utils/db_connection.php');
    require_once('./utils/access_control.php');

    header('Content-Type: application/json');

    # same lng sa teacher mn
    function logError($message) {
        error_log($message);
    }

    try {
        checkAccess(['student']);

        if (!isset($pdo) || !$pdo) {
            throw new Exception("Database connection failed");
        }

        function validatePasswordStrength($password) {
            $errors = [];
            
            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long";
            }
            
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = "Password must contain at least one uppercase letter";
            }
            
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = "Password must contain at least one lowercase letter";
            }
            
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "Password must contain at least one number";
            }
            
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                $errors[] = "Password must contain at least one special character";
            }
            
            return $errors;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['currentPassword'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                throw new Exception("All fields are required");
            }

            $passwordErrors = validatePasswordStrength($newPassword);
            if (!empty($passwordErrors)) {
                throw new Exception(implode(", ", $passwordErrors));
            }

            if (!isset($_SESSION['student_id'])) {
                throw new Exception("User not authenticated");
            }

            logError("Attempting to change password for student ID: " . $_SESSION['student_id']);

            $query = $pdo->prepare("SELECT password FROM users WHERE student_id = :student_id");
            
            if (!$query->execute(['student_id' => $_SESSION['student_id']])) {
                $errorInfo = $query->errorInfo();
                logError("Database Query Error: " . print_r($errorInfo, true));
                throw new Exception("Database query failed");
            }

            $user = $query->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                logError("No user found with student ID: " . $_SESSION['student_id']);
                throw new Exception("User not found");
            }

            if (!password_verify($currentPassword, $user['password'])) {
                logError("Password verification failed");
                throw new Exception("Current password is incorrect");
            }

            if (password_verify($newPassword, $user['password'])) {
                throw new Exception("New password cannot be the same as the current password");
            }

            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateQuery = $pdo->prepare("UPDATE users SET password = :new_password WHERE student_id = :student_id");
            $updateResult = $updateQuery->execute([
                'new_password' => $hashedNewPassword,
                'student_id' => $_SESSION['student_id']
            ]);

            if (!$updateResult) {
                $errorInfo = $updateQuery->errorInfo();
                logError("Update Password Error: " . print_r($errorInfo, true));
                throw new Exception("Failed to update password");
            }

            echo json_encode([
                'success' => true,
                'message' => 'Password successfully changed.'
            ]);

        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method Not Allowed'
            ]);
        }

    } catch (Exception $e) {
        logError("Password Change Error: " . $e->getMessage());

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } catch (PDOException $e) {
        logError("PDO Error: " . $e->getMessage());

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
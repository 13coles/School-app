<?php
    session_start();

    require_once('../utils/db_connection.php');
    require_once('../utils/access_control.php');

    header('Content-Type: application/json');

    function logError($message) {
        error_log($message);
    }

    try {
        # only teachers can access this change pass page
        checkAccess(['teacher']);

        if (!isset($pdo) || !$pdo) {
            throw new Exception("Database connection failed");
        }

        # function to validate password strength (server side)
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

        # logic to conditions to change password
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # get the data from the form request
            $currentPassword = $_POST['currentPassword'] ?? ''; # the defined current password
            $newPassword = $_POST['newPassword'] ?? ''; # the newly created password

            if (empty($currentPassword) || empty($newPassword)) {
                throw new Exception("All fields are required");
            }

            # validate new password
            $passwordErrors = validatePasswordStrength($newPassword);
            if (!empty($passwordErrors)) {
                throw new Exception(implode(", ", $passwordErrors));
            }

            # verify id that is set in the session data
            if (!isset($_SESSION['id'])) {
                throw new Exception("User not authenticated");
            }

            # for debugging of the password
            logError("Attempting to change password for teacher ID: " . $_SESSION['id']);

            # fetch current user's password hash from users table
            $query = $pdo->prepare("SELECT password FROM users WHERE id = :id");
            
            # execute the query and check for potential errors
            if (!$query->execute(['id' => $_SESSION['id']])) {
                $errorInfo = $query->errorInfo();
                logError("Database Query Error: " . print_r($errorInfo, true));
                throw new Exception("Database query failed");
            }

            $user = $query->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                logError("No user found with teacher ID: " . $_SESSION['id']);
                throw new Exception("User not found");
            }

            # verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                logError("Password verification failed");
                throw new Exception("Current password is incorrect");
            }

            # check if new password is the same as current password
            if (password_verify($newPassword, $user['password'])) {
                throw new Exception("New password cannot be the same as the current password");
            }

            # hash the new password too
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            # update the password in the users table
            $updateQuery = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :id");
            $updateResult = $updateQuery->execute([
                'new_password' => $hashedNewPassword,
                'id' => $_SESSION['id']
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
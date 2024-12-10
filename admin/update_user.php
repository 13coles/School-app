<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        
        $input = trim($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    function fetchUserDetails($user_id) {
        global $pdo;
    
        try {
            $query = $pdo->prepare("
                SELECT 
                    id, 
                    full_name, 
                    username, 
                    email, 
                    contact_number, 
                    user_role,
                    is_active
                FROM users 
                WHERE id = :user_id
            ");
    
            $query->execute(['user_id' => $user_id]);
            
            # checking any database query error
            if ($query->errorCode() !== '00000') {
                $errorInfo = $query->errorInfo();
                throw new Exception("Database query error: " . $errorInfo[2]);
            }
    
            $user = $query->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                throw new Exception("No user found with ID: $user_id");
            }
    
            return $user;
    
        } catch (Exception $e) {
            error_log("Error fetching user details: " . $e->getMessage());
            throw $e;
        }
    }

    function updateUserRecord($data) {
        global $pdo;

        try {
            $requiredFields = ['user_id', 'full_name', 'username', 'email', 'user_role'];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }

            $check_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $check_query->execute([$data['user_id']]);
            if ($check_query->fetchColumn() == 0) {
                throw new Exception("User record not found");
            }

            $username_check_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $username_check_query->execute([$data['username'], $data['user_id']]);
            if ($username_check_query->fetchColumn() > 0) {
                throw new Exception("Username is already in use");
            }

            $email_check_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
            $email_check_query->execute([$data['email'], $data['user_id']]);
            if ($email_check_query->fetchColumn() > 0) {
                throw new Exception("Email is already in use");
            }

            $pdo->beginTransaction();

            # preparing fields to update 
            $update_fields = [
                'full_name = :full_name', 
                'username = :username', 
                'email = :email', 
                'contact_number = :contact_number', 
                'user_role = :user_role',
            ];

            # if new password is present, change the current password of the user
            $password_update = '';
            if (!empty($data['password'])) {
                if (strlen($data['password']) < 8) {
                    throw new Exception("Password must be at least 8 characters long");
                }
                
                $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
                $update_fields[] = 'password = :password';
            }

            # prepare the query for SQL update and separate the data in the array
            # pass these data and update the corresponding field
            $query = $pdo->prepare("
                UPDATE users SET 
                " . implode(', ', $update_fields) . "
                WHERE id = :user_id
            ");

            $params = [
                ':user_id' => $data['user_id'],
                ':full_name' => $data['full_name'],
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':contact_number' => $data['contact_number'] ?? null,
                ':user_role' => $data['user_role'],
            ];

            # if new password was created, pass it to the password parameter to include in the update query
            if (!empty($data['password'])) {
                $params[':password'] = $hashed_password;
            }

            $result = $query->execute($params);

            if (!$result) {
                $pdo->rollBack();
                $errorInfo = $query->errorInfo();
                throw new Exception("Database update failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }

            $pdo->commit();

            return [
                'status' => 'success',
                'message' => 'User record updated successfully',
                'user_id' => $data['user_id']
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("User record update error: " . $e->getMessage());
            throw $e;
        }
    }

    try {
        $_GET = sanitizeInput($_GET);
        $_POST = sanitizeInput($_POST);

        # if get request, call the get function otherwise all the post function
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception("Invalid or missing user ID");
            }

            $user = fetchUserDetails(intval($_GET['id']));
            echo json_encode([
                'status' => 'success',
                'user' => $user
            ]);
        
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['user_id'])) {
                throw new Exception('User ID is required for update');
            }

            try {
                $result = updateUserRecord($_POST);
                echo json_encode($result);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            throw new Exception("Unsupported HTTP method");
        }
    } catch (Exception $e) {
        error_log("Error in update_user.php: " . $e->getMessage());
        
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
<?php
    require_once '../utils/db_connection.php';
    require_once '../utils/access_control.php';

    header('Content-Type: application/json');

    # function to sanitiz the incoming request input data 
    # securing the input data are valid without unnecessary spaces and special characters
    function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        
        $input = trim($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    function deleteUserRecord($user_id) {
        global $pdo;

        try {
            $pdo->beginTransaction();

            $check_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $check_query->execute([$user_id]);
            if ($check_query->fetchColumn() == 0) {
                throw new Exception("User record not found");
            }

            $delete_query = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $delete_query->execute([$user_id]);

            if (!$result) {
                throw new Exception("Failed to delete user record");
            }

            $pdo->commit();

            return [
                'status' => 'success',
                'message' => 'User deleted successfully',
                'user_id' => $user_id
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("User deletion error: " . $e->getMessage());
            throw $e;
        }
    }

    try {
        $_POST = sanitizeInput($_POST); # sanitize incoming post data

        # reject requests other than post requests for deletion
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Only POST requests are allowed");
        }

        # check the user id
        if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            throw new Exception("Invalid or missing user ID");
        }

        # delete user account
        $result = deleteUserRecord(intval($_POST['user_id']));
        
        echo json_encode($result);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
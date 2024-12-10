<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    try {
        $query = $pdo->prepare("
            SELECT 
                id, 
                username, 
                full_name, 
                email, 
                contact_number, 
                user_role, 
                is_active 
            FROM users 
            ORDER BY id DESC
        ");
        
        $query->execute();
        
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            'status' => 'success',
            'users' => $users
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        $errorResponse = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
        
        http_response_code(500);
        echo json_encode($errorResponse);
    }
    exit;
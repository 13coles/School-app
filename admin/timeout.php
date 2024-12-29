<?php
require_once '../utils/db_connection.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['log_id'])) {
        throw new Exception("Log ID is required");
    }

    $query = $pdo->prepare("
        UPDATE teacher_logs 
        SET time_out = :time_out 
        WHERE id = :log_id 
        AND time_out IS NULL
    ");

    $result = $query->execute([
        ':log_id' => $_POST['log_id'],
        ':time_out' => date('Y-m-d H:i:s')
    ]);

    if ($result && $query->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Time-out recorded successfully'
        ]);
    } else {
        throw new Exception("Failed to update time-out");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
exit;
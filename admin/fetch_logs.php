<?php
session_start();
require_once("../utils/db_connection.php");
require_once('../utils/access_control.php');

checkAccess(['admin']);

try {
    $query = "
        SELECT 
            al.id,
            al.user_id,
            u.full_name, 
            al.event,
            al.date
        FROM activity_logs al
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.date DESC;
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $logs
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
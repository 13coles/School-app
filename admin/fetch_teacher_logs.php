<?php
require_once '../utils/db_connection.php';
header('Content-Type: application/json');

try {
    $query = $pdo->prepare("
        SELECT 
            tl.id as log_id,
            t.teacher_id_num,
            tl.teacher_name,
            tl.time_in,
            tl.time_out
        FROM teacher_logs tl
        JOIN teachers t ON t.id = tl.teacher_id
        WHERE DATE(tl.time_in) = CURDATE()
        ORDER BY tl.time_in DESC
    ");
    
    $query->execute();
    $logs = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $data = array_map(function($log) {
        return [
            $log['teacher_id_num'],
            $log['teacher_name'],
            date('h:i A', strtotime($log['time_in'])),
            $log['time_out'] ? date('h:i A', strtotime($log['time_out'])) : '',
            $log['time_out'] ? '-' : '<button class="btn btn-warning btn-sm time-out" data-log-id="'.$log['log_id'].'">
                <i class="fas fa-clock"></i> Time-out
            </button>'
        ];
    }, $logs);
    
    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
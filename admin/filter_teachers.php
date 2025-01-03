<?php
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            id, 
            full_name, 
            grade, 
            section 
        FROM 
            teachers 
        ORDER BY 
            full_name
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'teachers' => $teachers
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
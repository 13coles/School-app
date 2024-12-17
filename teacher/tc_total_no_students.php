<?php
    
    require_once '../utils/db_connection.php';
    $query = "SELECT COUNT(*) AS total_students FROM students";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_students = $result['total_students'];
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
?>
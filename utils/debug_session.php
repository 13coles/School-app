<?php
    session_start();
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    try {
        $query = $pdo->query("SELECT 1");
    } catch (Exception $e) {
        $dbConnection = 'Failed: ' . $e->getMessage();
    }

    $debug = [
        'session_id' => session_id(),
        'session_data' => $_SESSION,
        'database_connection' => isset($dbConnection) ? $dbConnection : 'Connected',
    ];

    echo json_encode($debug, JSON_PRETTY_PRINT);
    exit;
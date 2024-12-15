<?php

// Ensure no previous output
if (ob_get_level()) {
    ob_clean();
}

require_once '../utils/db_connection.php';
session_start();

// Set proper headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Function to send JSON response
function sendJsonResponse($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['attendance'] = $data;
    }
    
    // Ensure JSON is valid
    $jsonResponse = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if ($jsonResponse === false) {
        // JSON encoding failed
        $errorResponse = json_encode([
            'status' => 'error',
            'message' => 'JSON encoding failed: ' . json_last_error_msg()
        ]);
        echo $errorResponse;
    } else {
        echo $jsonResponse;
    }
    exit;
}

try {
    // Debug: Log session and input
    error_log('Session Student ID: ' . print_r($_SESSION, true));
    error_log('GET Parameters: ' . print_r($_GET, true));

    // Ensure the student is logged in
    if (!isset($_SESSION['student_id'])) {
        sendJsonResponse('error', 'Unauthorized access. Please log in.');
    }

    // Get the logged-in student's ID
    $studentId = $_SESSION['student_id'];

    // Get the date from the request, default to today if not provided
    $date = $_GET['date'] ?? date('Y-m-d');

    // Comprehensive query to fetch attendance for the specific student
    $query = "
        SELECT 
            a.id AS attendance_record_id,
            a.student_id, 
            a.attendance_date,
            a.attendance,
            s.lrn, 
            s.full_name, 
            s.sex, 
            s.grade, 
            s.section
        FROM 
            attendance a
        JOIN 
            students s ON a.student_id = s.id
        WHERE 
            a.student_id = :student_id
    ";

    // Add date filter if a specific date is provided
    if ($date) {
        $query .= " AND a.attendance_date = :attendance_date";
    }

    $query .= " ORDER BY a.attendance_date DESC";

    // Prepare parameters
    $params = [':student_id' => $studentId];
    if ($date) {
        $params[':attendance_date'] = $date;
    }

    // Debug: Log query and params
    error_log('Query: ' . $query);
    error_log('Params: ' . print_r($params, true));

    // Prepare and execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log fetched records
    error_log('Fetched Records: ' . print_r($attendanceRecords, true));

    // Return the response
    if (empty($attendanceRecords)) {
        sendJsonResponse('success', 'No records found', []);
    } else {
        sendJsonResponse('success', 'Records found', $attendanceRecords);
    }

} catch (PDOException $e) {
    // Specific database error handling
    error_log('PDO Error: ' . $e->getMessage());
    error_log('PDO Error Code: ' . $e->getCode());
    error_log('PDO Error Trace: ' . $e->getTraceAsString());
    
    sendJsonResponse('error', 'Database error: ' . $e->getMessage());

} catch (Exception $e) {
    // General error handling
    error_log('General Error: ' . $e->getMessage());
    error_log('Error Trace: ' . $e->getTraceAsString());
    
    sendJsonResponse('error', $e->getMessage());
}
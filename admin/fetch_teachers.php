<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    try {
        // Prepare the SQL query to fetch teacher records
        $query = $pdo->prepare("
            SELECT 
                id,
                teacher_id_num, 
                full_name, 
                birth_date, 
                sex, 
                contact_number, 
                grade, 
                section 
            FROM teachers
        ");
        
        // Execute the query
        $query->execute();

        // Fetch all results
        $teachers = $query->fetchAll(PDO::FETCH_ASSOC);

        // Check if any teachers were found
        if ($teachers) {
            echo json_encode([
                'status' => 'success',
                'teachers' => $teachers,
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'teachers' => [],
                'message' => 'No teachers found'
            ]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch teachers'
        ]);
    }
    exit;
<?php
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

function getAssignedStudents($grade, $section) {
    global $pdo;

    try {
        // Add more detailed error logging
        error_log("Attempting to fetch students for Grade: $grade, Section: $section");

        // Add debugging query to check the exact conditions
        $debugQuery = $pdo->prepare("
            SELECT COUNT(*) as student_count
            FROM students 
            WHERE grade = :grade AND section = :section
        ");
        
        $debugQuery->execute([
            ':grade' => $grade, 
            ':section' => $section
        ]);
        
        $countResult = $debugQuery->fetch(PDO::FETCH_ASSOC);
        error_log("Total students matching criteria: " . $countResult['student_count']);

        // Main query
        $query = $pdo->prepare("
            SELECT 
                id, 
                full_name,
                lrn
            FROM students 
            WHERE grade = :grade AND section = :section
        ");
        
        // Use named parameters for better error tracking
        $query->execute([
            ':grade' => $grade, 
            ':section' => $section
        ]);
        
        $students = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Log the number of students found
        error_log("Students found: " . count($students));

        // Return JSON response
        echo json_encode([
            'status' => 'success',
            'students' => $students,
            'debug' => [
                'grade' => $grade,
                'section' => $section,
                'total_matching_students' => $countResult['student_count']
            ]
        ]);
    } catch (PDOException $e) {
        // Log the specific PDO error
        error_log("PDO Error in getAssignedStudents: " . $e->getMessage());
        
        // Return error response with more details
        echo json_encode([
            'status' => 'error',
            'message' => 'Could not fetch students',
            'error_details' => $e->getMessage()
        ]);
    } catch (Exception $e) {
        // Log any other unexpected errors
        error_log("Unexpected Error in getAssignedStudents: " . $e->getMessage());
        
        echo json_encode([
            'status' => 'error',
            'message' => 'An unexpected error occurred',
            'error_details' => $e->getMessage()
        ]);
    }
}

// Validate input parameters
if (!isset($_GET['grade']) || !isset($_GET['section'])) {
    error_log("Missing grade or section parameter");
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing grade or section parameter'
    ]);
    exit;
}

// Sanitize input using filter_input
$grade = filter_input(INPUT_GET, 'grade', FILTER_UNSAFE_RAW, 
    FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK
);
$section = filter_input(INPUT_GET, 'section', FILTER_UNSAFE_RAW, 
    FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK
);

// Check for empty values
if (empty($grade) || empty($section)) {
    error_log("Empty grade or section parameter");
    echo json_encode([
        'status' => 'error',
        'message' => 'Grade and section cannot be empty'
    ]);
    exit;
}

// Call the function
getAssignedStudents($grade, $section);
exit;
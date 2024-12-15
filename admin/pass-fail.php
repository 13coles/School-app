<?php
    require_once '../utils/db_connection.php';

    // SQL query to calculate passed and failed students based on the general average
    $query = "
        SELECT 
            COUNT(DISTINCT CASE 
                WHEN (COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4 >= 75 
                THEN sc.student_id 
                END) AS passed_students,
            COUNT(DISTINCT CASE 
                WHEN (COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4 < 75 
                THEN sc.student_id 
                END) AS failed_students
        FROM 
            student_card sc
        JOIN
            students s ON s.id = sc.student_id;
    ";

    try {
        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $passed_students = $result['passed_students'];
        $failed_students = $result['failed_students'];
    } catch (PDOException $e) {
        // Handle error in case of failure
        die("Query failed: " . $e->getMessage());
    }

?>

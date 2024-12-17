<?php

    require_once '../utils/db_connection.php';

    $query = "
        SELECT 
            s.id,
            s.lrn,
            s.full_name,
            COUNT(sc.subject_id) AS total_subjects,  -- Count total number of subjects
            SUM((COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4) AS total_final_grades,  -- Sum of final grades
            (SUM((COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4) / COUNT(sc.subject_id)) AS general_avg  -- Calculate final average
        FROM 
            student_card sc
        JOIN 
            students s ON s.id = sc.student_id
        GROUP BY 
            s.id
        ORDER BY 
            general_avg DESC
        LIMIT 10
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
?>
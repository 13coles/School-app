<?php
    require_once '../utils/db_connection.php';

    // SQL query to calculate general average and count students with general_avg <= 75
    $query = "
        SELECT 
            COUNT(*) AS students_below_75
        FROM (
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
            HAVING
                general_avg <= 75  -- Filter students with general average less than or equal to 75
        ) AS students_below_75
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $students_below_75 = $result['students_below_75'];
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
?>
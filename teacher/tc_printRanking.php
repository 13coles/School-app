<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);

    $grade = isset($_GET['grade']) ? $_GET['grade'] : null;
    $section = isset($_GET['section']) ? $_GET['section'] : null;

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
        WHERE
            (:grade IS NULL OR s.grade = :grade) 
            AND (:section IS NULL OR s.section = :section)
        GROUP BY 
            s.id
        ORDER BY 
            general_avg DESC;  -- Order by the general average (highest to lowest)
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }

    // Fetch available grades and sections for filtering
    try {
        $gradesQuery = "SELECT DISTINCT grade FROM students";
        $stmt = $pdo->query($gradesQuery);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        $sectionsQuery = "SELECT DISTINCT section FROM students";
        $stmt = $pdo->query($sectionsQuery);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 
    } catch (PDOException $e) {
        die("Error fetching data for grades and sections: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .report-header h1, .report-header p {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        @media print {
            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <img src="../assets/img/images.jfif" alt="School Logo">
        <h1>Sewahon National High School</h1>
        <p>School ID:302770</p>
        <p>School Address: Brgy Sewahon 1, Sagay City</p>
    </div>

    <h4>Student Ranking Report</h4>
    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Full Name</th>
                <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rank = 1;
            foreach ($students as $student) {
                echo "<tr>";
                echo "<td>" . $rank++ . "</td>";
                echo "<td>" . htmlspecialchars($student['full_name']) . "</td>";
                echo "<td>" . number_format($student['general_avg'], 2) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
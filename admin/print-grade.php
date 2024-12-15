<?php
session_start();
require_once '../utils/db_connection.php';
require_once '../utils/access_control.php';

checkAccess(['admin']);

// Ensure `student_id` is provided
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student details
    $stmt = $pdo->prepare("SELECT full_name, grade, section FROM students WHERE id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $student = $stmt->fetch();

    if (!$student) {
        die("Student not found.");
    }

    // Fetch subjects
    $stmt = $pdo->prepare("
        SELECT s.subject_name, ss.subject_id
        FROM student_subject ss
        JOIN subjects s ON ss.subject_id = s.id
        WHERE ss.student_id = :student_id
    ");
    $stmt->execute(['student_id' => $student_id]);
    $subjects = $stmt->fetchAll();

    // Fetch student card grades
    $stmt = $pdo->prepare("SELECT * FROM student_card WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $student_card = $stmt->fetch();

} else {
    die("Student ID not provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report Card</title>
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
        <p>School ID:</p>
        <p>School Address | School Email:</p>
    </div>

    <h2 style="text-align:center;">Student Report Card</h2>
    <table id="gradesTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle bg-primary text-center">Subjects</th>
                <th colspan="8" class="text-center bg-primary">
                    <?php echo $student['full_name'] . " - Grade: " . $student['grade'] . " Section: " . $student['section']; ?>
                </th>
                <th rowspan="2" class="align-middle bg-primary text-center">Final Grade</th>
            </tr>
            <tr>
                <th colspan="2" class="text-center bg-warning">1st Quarter</th>
                <th colspan="2" class="text-center bg-warning">2nd Quarter</th>
                <th colspan="2" class="text-center bg-warning">3rd Quarter</th>
                <th colspan="2" class="text-center bg-warning">4th Quarter</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                // Initialize variables for calculating the general average
                $total_final_grades = 0;
                $subject_count = 0;
            ?>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo $subject['subject_name']; ?></td>

                    <!-- 1st Quarter -->
                    <td colspan="2" class="p-1">
                        <?php echo $student_card["1st_quarter"] ?? ''; ?>
                    </td>

                    <!-- 2nd Quarter -->
                    <td colspan="2" class="p-1">
                        <?php echo $student_card["2nd_quarter"] ?? ''; ?>
                    </td>

                    <!-- 3rd Quarter -->
                    <td colspan="2" class="p-1">
                        <?php echo $student_card["3rd_quarter"] ?? ''; ?>
                    </td>

                    <!-- 4th Quarter -->
                    <td colspan="2" class="p-1">
                        <?php echo $student_card["4th_quarter"] ?? ''; ?>
                    </td>

                    <?php 
                        // Calculate the final grade for the subject
                        $first_quarter = $student_card["1st_quarter"] ?? 0;
                        $second_quarter = $student_card["2nd_quarter"] ?? 0;
                        $third_quarter = $student_card["3rd_quarter"] ?? 0;
                        $fourth_quarter = $student_card["4th_quarter"] ?? 0;
                        $final_grade = round(($first_quarter + $second_quarter + $third_quarter + $fourth_quarter) / 4);

                        // Add final grade to total for general average calculation
                        $total_final_grades += $final_grade;
                        $subject_count++;
                    ?>

                    <!-- Final Grade -->
                    <td class="p-1">
                        <?php echo $final_grade; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php 
                // Calculate general average if there are subjects
                if ($subject_count > 0) {
                    $general_avg = round($total_final_grades / $subject_count);
                } else {
                    $general_avg = 0; // Default to 0 if no subjects
                }
            ?>

            <!-- Display General Average -->
            <tr>
                <td colspan="9" class="text-right"><strong>General Average</strong></td>
                <td class="p-1">
                    <?php echo $general_avg; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        // Automatically print the page when loaded
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);

    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        $stmt = $pdo->prepare("SELECT full_name, grade, section FROM students WHERE id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        $student = $stmt->fetch();
    
        if (!$student) {
            die("Student not found.");
        }

        // Fetch the subjects for the student
        $stmt = $pdo->prepare("
            SELECT s.subject_name, ss.subject_id
            FROM student_subject ss
            JOIN subjects s ON ss.subject_id = s.id
            WHERE ss.student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $subjects = $stmt->fetchAll();
        
        // Fetch all grades and status for the student
        $stmt = $pdo->prepare("
            SELECT * FROM student_card WHERE student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $student_cards = $stmt->fetchAll();
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

    <h4 style="text-align:center;">Student Report Card</h4>
    <p><strong>Student Name: </strong><?php echo $student['full_name'] . " " . $student['grade'] . " Section: " . $student['section']; ?></p>
    <table id="gradesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle  text-center">Learning Areas</th>
                                        <th colspan="8" class="text-center"  style="text-align:center;">
                                           Quarter's
                                        </th>
                                        <th rowspan="2" class="align-middle  text-center">Final Grade</th>
                                        <th rowspan="2" class="align-middle  text-center">Remarks</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">1st Quarter</th>
                                        <th colspan="2" class="text-center">2nd Quarter</th>
                                        <th colspan="2" class="text-center">3rd Quarter</th>
                                        <th colspan="2" class="text-center">4th Quarter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_final_grades = 0;
                                        $subject_count = 0;
                                        foreach ($subjects as $subject):
                                            // Get the grades and status for this subject
                                            $grades = array_filter($student_cards, function($card) use ($subject) {
                                                return $card['subject_id'] == $subject['subject_id'];
                                            });

                                            $grade = reset($grades); // Take the first (and only) grade for the subject
                                    ?>
                                        <tr>
                                            <td><?php echo $subject['subject_name']; ?></td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["1st_quarter"] ?? ''; ?>
                                            </td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["2nd_quarter"] ?? ''; ?>
                                            </td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["3rd_quarter"] ?? ''; ?>
                                            </td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["4th_quarter"] ?? ''; ?>
                                            </td>

                                            <?php
                                                $first_quarter = $grade["1st_quarter"] ?? 0;
                                                $second_quarter = $grade["2nd_quarter"] ?? 0;
                                                $third_quarter = $grade["3rd_quarter"] ?? 0;
                                                $fourth_quarter = $grade["4th_quarter"] ?? 0;
                                                $final_grade = ($first_quarter + $second_quarter + $third_quarter + $fourth_quarter) / 4;
                                                $final_grade_rounded = round($final_grade);

                                                $total_final_grades += $final_grade_rounded;
                                                $subject_count++;
                                            ?>
                                            <td class="p-1 text-center">
                                                <?php echo $final_grade_rounded; ?>
                                            </td>
                                            <td class="p-1 text-center">
                                                <?php echo $grade['status'] ?? ''; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php
                                        // Calculate the general average
                                        if ($subject_count > 0) {
                                            $general_avg = $total_final_grades / $subject_count;
                                            $general_avg_rounded = round($general_avg);
                                        } else {
                                            $general_avg_rounded = 0;
                                        }
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-right "><strong>General Average:</strong></td>
                                        <td colspan="3" class="p-1  text-center">
                                            <?php echo $general_avg_rounded; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                       <table  class="table table-hide">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Grading scale</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Outstanding</td>
                                <td>90-100</td>
                                <td>Paased</td>
                            </tr>
                            <tr>
                                <td>Very Satisfactory</td>
                                <td>85-90</td>
                                <td>Paased</td>
                            </tr>
                            <tr>
                                <td>Satisfactory</td>
                                <td>80-84</td>
                                <td>Paased</td>
                            </tr>
                            <tr>
                                <td>Failry Satisfactory</td>
                                <td>75-79</td>
                                <td>Paased</td>
                            </tr>
                            <tr>
                                <td>Did Not Meet Expectation</td>
                                <td>Below 75</td>
                                <td>Failed</td>
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
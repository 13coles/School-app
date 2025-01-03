
<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);
    // Get teacher_id from session
    $teacher_id = $_SESSION['teacher_id'];

    // Get the quarter and subject filter values from GET request (if set)
    $quarter = isset($_GET['quarter']) ? $_GET['quarter'] : '';
    $subject = isset($_GET['subject']) ? $_GET['subject'] : '';

    // Fetch class records for the logged-in teacher, applying filters if set
    $query = "
    SELECT 
        s.full_name,
        s.section,
        s.grade,
        sub.subject_name,
        cr.quarter,
        cr.ww_1, cr.ww_2, cr.ww_3, cr.ww_4, cr.ww_total,
        (cr.ww_1 + cr.ww_2 + cr.ww_3 + cr.ww_4) AS ww_score,
        cr.pt_1, cr.pt_2, cr.pt_3, cr.pt_4, cr.pt_total,
        (cr.pt_1 + cr.pt_2 + cr.pt_3 + cr.pt_4) AS pt_score,
        cr.exam_1, cr.exam_total,
        (cr.ww_total + cr.pt_total + cr.exam_total) AS final_grade
        
    FROM 
        class_record cr
    JOIN 
        students s ON cr.student_id = s.id
    JOIN 
        subjects sub ON cr.subject_id = sub.id
    WHERE
        cr.teacher_id = :teacher_id
        " . ($quarter ? " AND cr.quarter = :quarter" : "") . "
        " . ($subject ? " AND sub.subject_name = :subject" : "") . "
    ORDER BY 
        s.full_name
";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
    if ($quarter) {
        $stmt->bindParam(':quarter', $quarter, PDO::PARAM_STR);
    }
    if ($subject) {
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
    }
    $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class records report</title>
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
        h4{
            text-align: center;
            font-size: bold;
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
        .text-right{
                text-align: right;
            }
        @media print {
            table {
                page-break-inside: avoid;
            }
            .text-right{
                text-align: right;
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

    <h4>Class Records</h4>
    <div>
        <p style="display: inline-block; margin-right: 10px;">
            <strong>Quarter:</strong> <?= htmlspecialchars($quarter ? "Quarter $quarter" : 'All Quarters') ?>
        </p>
        <p style="display: inline-block;">
            <strong>Subject:</strong> <?= htmlspecialchars($subject ? $subject : 'All Subjects') ?>
        </p>
    </div>

    <table id="studentTable" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class=" align-middle text-center">Learner's Name</th>
                                            <th colspan="6" class="text-center">Written Works (30%)</th>
                                            <th colspan="6" class="text-center">Performance Test (50%)</th>
                                            <th colspan="2" class="text-center"> Exams (20%)</th>
                                            <th rowspan="2" class="align-middle text-center">Final Grade</th>
                                        </tr>
                                        <tr>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>Total</th>
                                            <th>PS</th>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>Total</th>
                                            <th>PS </th>
                                            <th>Total</th>
                                            <th>PS </th>
                                        </tr>
                                        <tr class="bg-secondary">
                                        <th rowspan="2" class="align-middle text-right">Perfect Score</th>
                                            <th>25</th>
                                            <th>25</th>
                                            <th>25</th>
                                            <th>25</th>
                                            <th>100</th>
                                            <th>100%</th>
                                            <th>50</th>
                                            <th>50</th>
                                            <th>50</th>
                                            <th>50</th>
                                            <th>200</th>
                                            <th>100%</th>
                                            <th>50</th>
                                            <th>100%</th>
                                            <th rowspan="2" class="align-middle text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['full_name']) ?>, <?= htmlspecialchars($row['grade']) ?> - <?= htmlspecialchars($row['section']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_1']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_2']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_3']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_4']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_score']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_total']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_1']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_2']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_3']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_4']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_score']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_total']) ?></td>
                                                <td><?= htmlspecialchars($row['exam_1']) ?></td>
                                                <td><?= htmlspecialchars($row['exam_total']) ?></td>
                                                <td><?= htmlspecialchars($row['final_grade']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
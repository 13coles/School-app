<?php
    session_start();
    require_once './utils/db_connection.php';
    require_once('./utils/access_control.php');
    
    checkAccess(['student']);

    // Ensure only the logged-in student can access their own record
    if (!isset($_SESSION['student_id'])) {
        die("Unauthorized access. Please log in.");
    }

    $student_id = $_SESSION['student_id'];

    $query = "
        SELECT 
            s.id,
            s.lrn,
            s.full_name,
            s.grade,
            s.section,
            COUNT(sc.subject_id) AS total_subjects,
            SUM((COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4) AS total_final_grades,
            (SUM((COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4) / COUNT(sc.subject_id)) AS general_avg
        FROM 
            student_card sc
        JOIN 
            students s ON s.id = sc.student_id
        WHERE
            s.id = :student_id
        GROUP BY 
            s.id
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        // Fetch detailed subject grades
        $subjectsQuery = "
            SELECT 
                sub.subject_name,
                sc.1st_quarter,
                sc.2nd_quarter,
                sc.3rd_quarter,
                sc.4th_quarter,
                ROUND((sc.1st_quarter + sc.2nd_quarter + sc.3rd_quarter + sc.4th_quarter) / 4, 2) AS final_grade
            FROM 
                student_card sc
            JOIN 
                subjects sub ON sc.subject_id = sub.id
            WHERE 
                sc.student_id = :student_id
        ";
        $subjectsStmt = $pdo->prepare($subjectsQuery);
        $subjectsStmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $subjectsStmt->execute();
        $subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);
        $subjectsStmt->closeCursor();

    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './components/header.php'; ?>
    <link rel="stylesheet" href="./vendor/almasaeed2010/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="./vendor/almasaeed2010/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="./vendor/almasaeed2010/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        .performance-summary {
            background-color: #f4f6f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include('./components/navbar.php'); ?>
        <?php include('./components/sidebar.php'); ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">My Performance Report</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Performance Report</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <?php include './utils/sessions.php' ?>
                    
                    <div class="performance-summary">
                        <h3>Student Details</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
                                <p><strong>LRN:</strong> <?= htmlspecialchars($student['lrn']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Grade:</strong> <?= htmlspecialchars($student['grade']) ?></p>
                                <p><strong>Section:</strong> <?= htmlspecialchars($student['section']) ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong>General Average:</strong> <?= number_format($student['general_avg'], 2) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Subject Grades</h3>
                            <div class="card-tools">
                                <a href="print-performance.php" class="btn btn-sm btn-primary">Print Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="gradesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>1st Quarter</th>
                                        <th>2nd Quarter</th>
                                        <th>3rd Quarter</th>
                                        <th>4th Quarter</th>
                                        <th>Final Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects as $subject): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                            <td><?= number_format($subject['1st_quarter'], 2) ?></td>
                                            <td><?= number_format($subject['2nd_quarter'], 2) ?></td>
                                            <td><?= number_format($subject['3rd_quarter'], 2) ?></td>
                                            <td><?= number_format($subject['4th_quarter'], 2) ?></td>
                                            <td><?= number_format($subject['final_grade'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./components/footer.php'); ?>
    </div>

    <!-- Scripts component -->
    <?php include('./components/scripts.php'); ?>
    
        <!-- DataTables & Plugins -->
        <script src="./vendor/almasaeed2010/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="./vendor/almasaeed2010/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="./vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="./vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="./vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    
    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            $('#gradesTable').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "searching": false,
                "ordering": true,
                "paging": false,
                "info": false,
                "columns": [
                    { "width": "30%" },   // Subject
                    { "width": "10%" },   // 1st Quarter
                    { "width": "10%" },   // 2nd Quarter
                    { "width": "10%" },   // 3rd Quarter
                    { "width": "10%" },   // 4th Quarter
                    { "width": "10%" }    // Final Grade
                ],
                "order": [[5, "desc"]]  // Order by final grade descending
            });

            // Print functionality
            $('#printReport').on('click', function() {
                window.print();
            });

            // Calculate and display additional performance metrics
            calculatePerformanceMetrics();
        });

        function calculatePerformanceMetrics() {
            // Example of additional performance calculation
            let grades = $('#gradesTable tbody tr').map(function() {
                return parseFloat($(this).find('td:last-child').text());
            }).get();

            let totalGrade = grades.reduce((a, b) => a + b, 0);
            let averageGrade = totalGrade / grades.length;

            let performanceCategory = '';
            if (averageGrade >= 90) performanceCategory = 'Excellent';
            else if (averageGrade >= 80) performanceCategory = 'Very Good';
            else if (averageGrade >= 70) performanceCategory = 'Good';
            else performanceCategory = 'Needs Improvement';

            $('#performanceCategory').text(performanceCategory);
        }
    </script>
</body>
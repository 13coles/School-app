<?php 
    session_start();
    require_once './utils/db_connection.php';
    require_once('./utils/access_control.php');

    checkAccess(['student']);

    // Ensure only the logged-in student can access their own data
    if (!isset($_SESSION['student_id'])) {
        die("Unauthorized access. Please log in.");
    }

    $student_id = $_SESSION['student_id'];

    try {
        // Fetch student's academic performance
        $performanceQuery = "
            SELECT 
                COUNT(sc.subject_id) AS total_subjects,
                ROUND(AVG((sc.1st_quarter + sc.2nd_quarter + sc.3rd_quarter + sc.4th_quarter) / 4), 2) AS general_avg,
                s.full_name,
                s.grade,
                s.section
            FROM 
                student_card sc
            JOIN 
                students s ON s.id = sc.student_id
            WHERE 
                s.id = :student_id
            GROUP BY 
                s.id
        ";
        $stmt = $pdo->prepare($performanceQuery);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->execute();
        $studentPerformance = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch subject-wise grades for chart
        $subjectsQuery = "
            SELECT 
                sub.subject_name,
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
        $subjectGrades = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }

    // Determine performance status
    $performanceStatus = 'Needs Improvement';
    $statusClass = 'warning';
    if ($studentPerformance['general_avg'] >= 90) {
        $performanceStatus = 'Excellent';
        $statusClass = 'success';
    } elseif ($studentPerformance['general_avg'] >= 80) {
        $performanceStatus = 'Very Good';
        $statusClass = 'info';
    } elseif ($studentPerformance['general_avg'] >= 75) {
        $performanceStatus = 'Good';
        $statusClass = 'primary';
    }
?>

<!DOCTYPE html>
<html lang="en">
    <?php include('./components/header.php');?>
    <head>
        <!-- Add Chart.js if not already included -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="./assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div>
            
            <!-- Navbar component -->
            <?php include('./components/navbar.php');?>
            <!-- Sidebar component -->
            <?php include('./components/student_sidebar.php');?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Dashboard - Welcome, <?= htmlspecialchars($studentPerformance['full_name']) ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-9">
                                <!-- Performance Cards -->
                                <div class="row flex justify-content-start align-items-center mb-4">
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box-clean info">
                                            <div class="icon">
                                                <i class="ion ion-ios-book text-info"></i>
                                            </div>
                                            <div class="inner">
                                                <h3><?= $studentPerformance['total_subjects'] ?></h3>
                                                <p>Total Subjects</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box-clean success">
                                            <div class="icon">
                                                <i class="ion ion-podium text-success"></i>
                                            </div>
                                            <div class="inner">
                                                <h3><?= $studentPerformance['general_avg'] ?><sup style="font-size: 0.6em">%</sup></h3>
                                                <p>Academic Performance</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box-clean <?= $statusClass ?>">
                                            <div class="icon">
                                                <i class="ion ion-stats-bars text-<?= $statusClass ?>"></i>
                                            </div>
                                            <div class="inner">
                                                <h3><?= $performanceStatus ?></h3>
                                                <p>Status</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student grade chart -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Subject Performance</h3>
                                            <a href="performance-report.php" class="text-primary">View Full Report</a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="subject-performance-chart" style="height: 400px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 d-flex align-items-stretch">
                                <div class="card card-success card-outline w-100">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Grade Distribution</h3>
                                            <a href="performance-report.php" class="text-success">View Details</a>
                                        </div>
                                        </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="chart-container flex-grow-1">
                                            <canvas id="grade-distribution-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.content -->
            </div>

            <?php include('./components/footer.php');?>
        </div>
        
        <?php include('./components/scripts.php');?>

        <script>
            $(document).ready(function() {
                const subjectNames = <?= json_encode(array_column($subjectGrades, 'subject_name')) ?>;
                const finalGrades = <?= json_encode(array_column($subjectGrades, 'final_grade')) ?>;
                
                // Categorize grades
                const gradeCategories = {
                    'Excellent (90-100)': finalGrades.filter(grade => grade >= 90).length,
                    'Very Good (80-89)': finalGrades.filter(grade => grade >= 80 && grade < 90).length,
                    'Good (75-79)': finalGrades.filter(grade => grade >= 75 && grade < 80).length,
                    'Needs Improvement (<75)': finalGrades.filter(grade => grade < 75).length
                };

                function calculatePerformanceInsights() {
                    const averageGrade = <?= $studentPerformance['general_avg'] ?>;
                    const totalSubjects = <?= $studentPerformance['total_subjects'] ?>;
                    
                    let performanceMessage = '';
                    if (averageGrade >= 90) {
                        performanceMessage = `Congratulations! You're an outstanding student with an excellent academic performance.`;
                    } else if (averageGrade >= 80) {
                        performanceMessage = `Great job! You're performing very well across your subjects.`;
                    } else if (averageGrade >= 75) {
                        performanceMessage = `You're doing good. Keep working to improve your grades.`;
                    } else {
                        performanceMessage = `You might need additional support. Consider seeking help from your teachers.`;
                    }

                    Swal.fire({
                        icon: 'info',
                        title: 'Your Academic Insights',
                        html: `
                            <p>${performanceMessage}</p>
                            <hr>
                            <small>
                                Total Subjects: ${totalSubjects}<br>
                                Average Grade: ${averageGrade.toFixed(2)}%<br>
                                Grade: <?= $studentPerformance['grade'] ?> | Section: <?= $studentPerformance['section'] ?>
                            </small>
                        `
                    });
                }

                calculatePerformanceInsights();
            });
        </script>
    </body>
</html>
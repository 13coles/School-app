<?php 
    session_start();

    require_once("../utils/db_connection.php");
    require_once('../utils/access_control.php');
    
    checkAccess(['admin']);

    function calculateOverallAttendancePercentage($month, $year) {
        global $pdo;
    
        $query = $pdo->prepare("
            SELECT 
                student_id, 
                COUNT(CASE WHEN attendance = 'present' THEN 1 END) AS days_present,
                COUNT(*) AS total_days
            FROM attendance
            WHERE MONTH(attendance_date) = :month AND YEAR(attendance_date) = :year
            GROUP BY student_id
        ");
    
        $query->execute([':month' => $month, ':year' => $year]);
        $attendanceRecords = $query->fetchAll(PDO::FETCH_ASSOC);
    
        $totalDaysPresent = 0;
        $totalSchoolDays = 0;
    
        foreach ($attendanceRecords as $record) {
            $totalDaysPresent += $record['days_present'];
            $totalSchoolDays += $record['total_days'];
        }
    
        if ($totalSchoolDays > 0) {
            return round(($totalDaysPresent / $totalSchoolDays) * 100, 2); 
        } else {
            return 0; // No school days
        }
    }
    
    $currentMonth = date('n'); 
    $currentYear = date('Y'); 
    $overallAttendancePercentage = calculateOverallAttendancePercentage($currentMonth, $currentYear);
?>

<!-- Dashboard page -->
<!DOCTYPE html>
<html lang="en">
    <?php include('../components/header.php')?>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <!-- <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="../assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div> -->
            
            <!-- Navbar component -->
            <?php include('../components/navbar.php');?>
            <!-- Sidebar component -->
            <?php include('../components/sidebar.php');?>


            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Dashboard</h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>

                <!-- Small boxes (Stat box) -->
                <div class="row flex justify-content-center align-items-center px-3 mb-4">
                    <?php include 'total_no_students.php'?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean info">
                            <div class="icon">
                                <i class="ion ion-person-stalker text-info"></i>
                            </div>
                            <div class="inner">
                                <h3><?php echo $total_students; ?></h3>
                                <p>Total Students</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean success">
                            <div class="icon">
                                <i class="ion ion-stats-bars text-success"></i>
                            </div>
                            <div class="inner">
                                <!-- <h3><?php echo htmlspecialchars($overallAttendancePercentage); ?><sup style="font-size: 0.6em">%</sup></h3> -->
                                <h3>10</h3>
                                <p>Total Subjects</p>
                            </div>
                        </div>
                    </div>
                    <?php include 'pass.php' ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean warning">
                            <div class="icon">
                                <i class="ion ion-person-add text-warning"></i>
                            </div>
                            <div class="inner">
                            <h3><?= htmlspecialchars($students_above_75) ?></h3>
                                <p>Total Passed</p>
                            </div>
                        </div>
                    </div>
                    <?php include 'fail.php' ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean danger">
                            <div class="icon">
                                <i class="ion ion-clipboard text-danger"></i>
                            </div>
                            <div class="inner">
                            <h3><?= htmlspecialchars($students_below_75) ?></h3>
                                <p>Total Failed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    
                                    <?php include 'top-10.php'?>

                                    <div class="col-lg-6">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header border-0">
                                                <div class="d-flex justify-content-between">
                                                    <h3 class="card-title">Top 10 Performing Students</h3>
                                                    <a href="performance.php" class="text-primary">View Report</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Rank</th>
                                                            <th>Student Name</th>
                                                            <th>General Average</th>
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
                                            </div>
                                        </div>
                                    </div>


                                    <!-- pie chart conainer -->
                                    <div class="col-lg-6">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header border-0">
                                                <div class="d-flex justify-content-between">
                                                    <h3 class="card-title">Attendance Report</h3>
                                                    <a href="attendance.php" class="text-primary">View Report</a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card-body">
                                                        <canvas id="attendanceChart" style="min-height: 400px;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.content -->
            </div>

            <?php include('../components/footer.php');?>
        </div>

        <?php include('../components/scripts.php');?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                fetch('attendance_graph.php')
                .then(response => response.json())
                .then(stats => {
                    console.log('Received stats:', stats);
                    
                    if (stats.error) {
                        console.error('Error from server:', stats.error);
                        return;
                    }

                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    
                    const data = {
                        labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
                        datasets: [
                            {
                                label: 'Present',
                                backgroundColor: '#28a745',
                                data: stats.present || []
                            },
                            {
                                label: 'Absent',
                                backgroundColor: '#dc3545',
                                data: stats.absent || []
                            },
                            {
                                label: 'Late',
                                backgroundColor: '#ffc107',
                                data: stats.late || []
                            }
                        ]
                    };

                    const config = {
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Grade Levels'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Students'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Student Attendance Distribution by Grade Level'
                                },
                                legend: {
                                    position: 'top'
                                }
                            },
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        }
                    };

                    new Chart(ctx, config);
                })
                .catch(error => console.error('Error loading attendance data:', error));
            });
        </script>
    </body>
</html>

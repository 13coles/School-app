<?php 
    session_start();

    require_once('../utils/access_control.php');

    checkAccess(['teacher']);
    
?>

<!DOCTYPE html>
<html lang="en">
    <?php include('../components/header.php')?>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="../assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div>
            
            <!-- Navbar component -->
            <?php include('../components/navbar.php');?>
            <!-- Sidebar component -->
            <!-- Same here -->
             <?php include('../components/teacher_sidebar.php');?>

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
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                <?php include 'tc_total_no_students.php' ?>
                <!-- Small boxes (Stat box) -->
                <div class="row flex justify-content-center align-items-center px-3 mb-4">
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
                                <h3>53<sup style="font-size: 0.6em">%</sup></h3>
                                <p>Academic Performance</p>
                            </div>
                        </div>
                    </div>
                    <?php include 'tc_pass.php' ?>
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
                    <?php include 'tc_fail.php' ?>
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
                                                    <h3 class="card-title">Perfect Attendance Report</h3>
                                                    <a href="javascript:void(0);" class="text-primary">View Report</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                
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
            <!-- Footer component -->
            <?php include('../components/footer.php');?>
        </div>

        <?php include('../components/scripts.php');?>
    </body>


</html>

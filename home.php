<?php 
    session_start();

    require_once('./utils/access_control.php');

    checkAccess(['student']);
?>


<!DOCTYPE html>
<html lang="en">
    <?php include('./components/header.php');?>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="./assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div>
            
            <!-- Navbar component -->
            <?php include('./components/navbar.php');?>
            <!-- Sidebar component -->
            <!-- if mag dynamic na ang sidbar pwede nlng ni mailisan "< include('./components/sidebar.php')>" -->
            <?php include('./components/student_sidebar.php');?>


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

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-9">
                                <!-- Small boxes (Stat box) -->
                                <div class="row flex justify-content-start align-items-center mb-4">
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box-clean info">
                                            <div class="icon">
                                                <i class="ion ion-ios-book text-info"></i>
                                            </div>
                                            <div class="inner">
                                                <h3>8</h3>
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
                                                <h3>53<sup style="font-size: 0.6em">%</sup></h3>
                                                <p>Academic Performance</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 col-6">
                                        <div class="small-box-clean warning">
                                            <div class="icon">
                                                <i class="ion ion-close-circled text-warning"></i>
                                            </div>
                                            <div class="inner">
                                                <h3>Failed</h3>
                                                <p>Status</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student grade chart -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Passed & Failed Student Records</h3>
                                            <a href="javascript:void(0);" class="text-primary">View Report</a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="visitors-chart" style="height: 400px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 d-flex align-items-stretch">
                                <div class="card card-success card-outline w-100">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Student Grade Distribution</h3>
                                            <a href="javascript:void(0);" class="text-success">View Details</a>
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
    </body>
</html>
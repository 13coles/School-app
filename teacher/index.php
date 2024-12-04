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
                                <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>

                <!-- Small boxes (Stat box) -->
                <div class="row flex justify-content-center align-items-center px-3 mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean info">
                            <div class="icon">
                                <i class="ion ion-person-stalker text-info"></i>
                            </div>
                            <div class="inner">
                                <h3>150</h3>
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
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean warning">
                            <div class="icon">
                                <i class="ion ion-person-add text-warning"></i>
                            </div>
                            <div class="inner">
                                <h3>44</h3>
                                <p>Total Passed</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box-clean danger">
                            <div class="icon">
                                <i class="ion ion-clipboard text-danger"></i>
                            </div>
                            <div class="inner">
                                <h3>65</h3>
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
                                    <!-- Bar chart -->
                                    <div class="col-lg-6">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header border-0">
                                                <div class="d-flex justify-content-between">
                                                    <h3 class="card-title">Passed & Failed Student Records</h3>
                                                    <a href="javascript:void(0);" class="text-primary">View Report</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="chart-container">
                                                    <canvas id="visitors-chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pie chart container -->
                                    <div class="col-lg-6">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header border-0">
                                                <div class="d-flex justify-content-between">
                                                    <h3 class="card-title">Student Performance By Section (Group 1)</h3>
                                                    <a href="javascript:void(0);" class="text-primary">View Report</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row d-flex justify-content-center">
                                                    <!-- First Chart -->
                                                    <div class="col-md-5 text-center">
                                                        <h5 class="mb-2">Charity</h5>
                                                        <div class="chart-container pie-chart-container"> 
                                                            <canvas id="charity"></canvas>
                                                        </div>
                                                        <div class="performance-legend">
                                                            <span class="badge bg-success mr-1">Passed: 65%</span>
                                                            <span class="badge bg-danger">Failed: 35%</span>
                                                        </div>
                                                    </div>

                                                    <!-- Second Chart -->
                                                    <div class="col-md-5 text-center">
                                                        <h5 class="mb-2">Humility</h5>
                                                        <div class="chart-container pie-chart-container"> 
                                                            <canvas id="humility"></canvas>
                                                        </div>
                                                        <div class="performance-legend">
                                                            <span class="badge bg-success mr-1">Passed: 72%</span>
                                                            <span class="badge bg-danger">Failed: 28%</span>
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
                </div>
                <!-- /.content -->
            </div>
            <!-- Footer component -->
            <?php include('../components/footer.php');?>
        </div>

        <?php include('../components/scripts.php');?>
    </body>

    <!-- Static Data for charts -->
    <!-- Gn butangan ko lng dri static data sa charts para may visuals lng, revise ko lng if needed no dynamic functions yet.  -->
    <script src="../assets/js/index.js"></script>
</html>

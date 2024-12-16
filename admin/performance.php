<?php
session_start();
require_once '../utils/db_connection.php';
require_once('../utils/access_control.php');

checkAccess(['admin']);


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include '../components/header.php'; ?>
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
        <style>
            .editable-grade {
                width: 70px;
                text-align: center;
                border: 1px solid #ced4da;
                background-color: transparent;
                padding: 5px;
                margin: 0 auto;
            }
            .editable-grade:focus {
                outline: none;
                border-color: #007bff;
                box-shadow: 0 0 5px rgba(0,123,255,0.3);
            }
        </style>
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="../assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div>

            <!-- Navbar component -->
            <?php include('../components/navbar.php');?>
            <!-- Sidebar component -->
            <?php include('../components/sidebar.php');?>

             <!-- Content Wrapper -->
             <div class="content-wrapper">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Student Performance </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="students.php">Students Table</a></li>
                                    <li class="breadcrumb-item active">Student Performance </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <?php include '../utils/sessions.php' ?>
                        <div class="card">
                            <div class="card-header">
                            
                            </div>
                            <form method="GET" action="">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="student_name">Student</label>
                                        
                                    </div>

                                    <div class="form-group col-md-3">
                                        
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary mt-4 pb-2">Filter</button>
                                    </div>
                                </div>
                            </form>
                            <div class="card-body">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer component -->
            <?php include('../components/footer.php');?>
        </div>
        <!-- Scripts component -->
        <?php include('../components/scripts.php');?>
        
        <!-- DataTables & Plugins -->
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    </body>
</html>

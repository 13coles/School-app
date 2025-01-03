<?php
    session_start();

    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['admin']);
?>

<!-- User management page -->
<!DOCTYPE html>
<html lang="en">
    <head>
            <?php include('../components/header.php')?>
            <!-- DataTables CSS -->
            <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
            <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
            <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
            <link rel="stylesheet" href="../vendor/almasaeed2010/adminlte/plugins/select2/css/select2.min.css">
            <link rel="stylesheet" href="../assets//css/students_modal.css">
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

                                <h1 class="m-0">Teacher Time-in & Time-out</h1>

                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Teacher Information</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-end align-items-center">

                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#timeinModal">

                                        <i class="fas fa-clock mr-1"></i> Time-in
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">

                                <table id="teacherLogTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Teacher Identification Number</th>
                                            <th>Full Name</th>
                                            <th class="text-center">Time In</th>
                                            <th class="text-center">Time Out</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <!-- Rendered in javascript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Time in form -->
            <div class="modal fade" id="timeinModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title font-semibold">Teacher Time-in</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addTeacherForm">
                            <div class="modal-body">
                                <!-- Teachers dropdown -->
                                <div class="form-group">
                                    <label for="teacher_select">Teacher</label>
                                    <select class="form-control custom-select" id="teacher_select" name="teacher_id" style="width: 100%;" required>
                                        <option value="">Select a teacher</option>
                                    </select>
                                </div>

                                <!-- Time in -->
                                <div class="form-group">
                                    <label for="time_in">Time-in</label>
                                    <input type="time" class="form-control" id="time_in" name="time_in" required>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-clock mr-1"></i> Record</button>
                            </div>
                        </form>
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
        <script src="../vendor/almasaeed2010/adminlte/plugins/select2/js/select2.full.min.js"></script>

        <script src="../assets/js/teacher_logs.js"></script>
    </body>
</html>
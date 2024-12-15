<?php 
    session_start();

    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);
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
            <?php include('../components/teacher_sidebar.php');?>

             <!-- Content Wrapper -->
             <div class="content-wrapper">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Subjects & Grades</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Performance</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <table id="gradesTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center">Student Name</th>
                                            <th colspan="8" class="text-center">Subjects</th>
                                            <th rowspan="2" class="align-middle text-center">Actions</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Filipino</th>
                                            <th class="text-center">English</th>
                                            <th class="text-center">Math</th>
                                            <th class="text-center">Science</th>
                                            <th class="text-center">Aral Pan</th>
                                            <th class="text-center">MAPEH</th>
                                            <th class="text-center">ESP</th>
                                            <th class="text-center">TLE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>John Doe</td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="Filipino">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="English">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="Math">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="Science">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="Aral Pan">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="MAPEH">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="ESP">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control editable-grade" data-subject="TLE">
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary btn-edit-grades" data-toggle="tooltip" title="Edit Grades">
                                                    <i class="fas fa-edit mr-1"></i>Edit Grade
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
        <script src="../assets/js/view_grades.js"></script>
    </body>
</html>
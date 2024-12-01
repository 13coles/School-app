<?php session_start()?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include('../components/header.php')?>
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="../assets/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
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
                                <h1 class="m-0">Student Records</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Students</li>
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
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStudentModal">
                                        <i class="fas fa-plus-circle mr-1"></i> Add Student
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <table id="studentTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th class="text-center">Section</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td>John Doe</td>
                                            <td>john@example.com</td>
                                            <td class="text-center">Charity</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">Active</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="/admin/view_grades.php" class="btn btn-sm btn-info" data-toggle="tooltip" title="View Grades">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" data-toggle="tooltip" title="Attendance">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-danger" data-toggle="tooltip" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Student Modal -->
            <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Add Student Form -->
                            <form>
                                <div class="form-group">
                                    <label for="studentName">Full Name</label>
                                    <input type="text" class="form-control" id="studentName" placeholder="Enter student name">
                                </div>
                                
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Save Student</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Modal -->
            <div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-labelledby="attendanceModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title" id="attendanceModalLabel">
                                <i class="fas fa-calendar-check mr-2 text-success"></i>
                                Attendance for John Doe
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label class="d-block">Attendance Status</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="presentRadio" name="attendanceStatus" class="custom-control-input" value="present">
                                            <label class="custom-control-label text-success" for="presentRadio">
                                                <i class="fas fa-check-circle mr-1"></i>Present
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="absentRadio" name="attendanceStatus" class="custom-control-input" value="absent">
                                            <label class="custom-control-label text-danger" for="absentRadio">
                                                <i class="fas fa-times-circle mr-1"></i>Absent
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="attendanceDate">Date</label>
                                        <input type="date" class="form-control" id="attendanceDate" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="saveAttendance">
                                <i class="fas fa-save mr-1"></i>Save Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('../components/footer.php');?>
        </div>

        <?php include('../components/scripts.php');?>
        
        <!-- DataTables & Plugins -->
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/js/pages/student_record.js"></script>
    </body>
</html>
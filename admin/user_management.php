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
                                <h1 class="m-0">User Management</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">User Management</li>
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
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
                                        <i class="fas fa-plus-circle mr-1"></i> Create User
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <table id="studentTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th>Full Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Contact Number</th>
                                            <th class="text-center">Role</th>
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

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title font-weight-bold" id="addUserModalLabel">
                                <i class="fas fa-user-plus mr-2"></i>Create New User Account
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <form id="createUserForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Full name of user field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                                    placeholder="Enter full name" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- User role field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_role">User Role <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
                                                <select class="form-control" id="user_role" name="user_role" required>
                                                    <option value="">Select User Role</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="teacher">Teacher</option>
                                                    <option value="student">Student</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">   
                                    <!-- Email field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                    placeholder="Enter email address" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_number">Contact Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                                    placeholder="Enter contact number">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Username field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="username" name="username" 
                                                    placeholder="Choose a username" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control" id="password" name="password" 
                                                    placeholder="Create a strong password" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password" style="cursor: pointer;">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                Password must be at least 8 characters long
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title font-weight-bold" id="editUserModalLabel">
                                <i class="fas fa-user-edit mr-2"></i>Edit User Account
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <form id="editUserForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="edit_user_id" name="user_id">
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Full name of user field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edit_full_name">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="edit_full_name" name="full_name" 
                                                    placeholder="Enter full name" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- User role field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edit_user_role">User Role <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                </div>
                                                <select class="form-control" id="edit_user_role" name="user_role" required>
                                                    <option value="">Select User Role</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="teacher">Teacher</option>
                                                    <option value="student">Student</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">   
                                    <!-- Email field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edit_email">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" class="form-control" id="edit_email" name="email" 
                                                    placeholder="Enter email address" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edit_contact_number">Contact Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="tel" class="form-control" id="edit_contact_number" name="contact_number" 
                                                    placeholder="Enter contact number">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Username field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edit_username">Username <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="edit_username" name="username" 
                                                    placeholder="Choose a username" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edit_password">New Password</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control" id="edit_password" name="password" 
                                                    placeholder="Leave blank to keep current password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password" style="cursor: pointer;">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                Leave blank if you don't want to change the password
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Update
                                </button>
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
        <script src="../assets/js/user.js"></script>
    </body>
</html>
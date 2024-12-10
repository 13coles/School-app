<?php 
    session_start();

    require_once('../utils/db_connection.php');
    require_once('../utils/access_control.php');

    checkAccess(['teacher']);
?>


<!DOCTYPE html>
<html lang="en">
    <?php include('../components/header.php');?>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="../assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div>
            
            <!-- Navbar component -->
            <?php include('../components/navbar.php');?>
            <!-- Sidebar component -->
            <?php include('../components/student_sidebar.php');?>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Account Settings</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Change Password</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-lock mr-2"></i>Change Password
                                                </h3>
                                            </div>
                                            
                                            <form id="changePasswordForm">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="currentPassword">Current Password</label>
                                                                <div class="input-group">
                                                                    <input type="password" 
                                                                        class="form-control" 
                                                                        id="currentPassword" 
                                                                        name="currentPassword" 
                                                                        placeholder="Enter current password" 
                                                                        required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text toggle-password" 
                                                                            data-target="currentPassword">
                                                                            <i class="fas fa-eye"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="newPassword">New Password</label>
                                                                <div class="input-group">
                                                                    <input type="password" 
                                                                        class="form-control" 
                                                                        id="newPassword" 
                                                                        name="newPassword" 
                                                                        placeholder="Enter new password" 
                                                                        required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text toggle-password" 
                                                                            data-target="newPassword">
                                                                            <i class="fas fa-eye"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <small class="form-text text-muted">
                                                                    Password must be at least 8 characters long
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="confirmPassword">Confirm New Password</label>
                                                                <div class="input-group">
                                                                    <input type="password" 
                                                                        class="form-control" 
                                                                        id="confirmPassword" 
                                                                        name="confirmPassword" 
                                                                        placeholder="Confirm new password" 
                                                                        required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text toggle-password" 
                                                                            data-target="confirmPassword">
                                                                            <i class="fas fa-eye"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div id="passwordStrength" class="mb-3">
                                                                <small>Password Strength: 
                                                                    <span id="strengthText" class="text-muted">Not Assessed</span>
                                                                </small>
                                                                <div class="progress" style="height: 5px;">
                                                                    <div id="strengthBar" 
                                                                        class="progress-bar" 
                                                                        role="progressbar" 
                                                                        style="width: 0%;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-footer">
                                                    <button type="submit" 
                                                            class="btn btn-primary btn-block" 
                                                            id="changePasswordBtn">
                                                        <i class="fas fa-lock mr-2"></i>Change Password
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="card card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle mr-2"></i>Password Guidelines
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                        Minimum 8 characters long
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                        Contains at least one uppercase letter
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                        Contains at least one lowercase letter
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                        Contains at least one number
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                        Contains at least one special character
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer component -->
            <?php include('../components/footer.php');?>
        </div>
        
        <?php include('../components/scripts.php');?>

        <script src="../assets/js/teacher_settings.js"></script>
    </body>
</html>
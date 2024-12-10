<?php 
    session_start();

    require_once('utils/access_control.php');

    checkAccess(['student']);
?>

<!DOCTYPE html>
<html lang="en">
    <?php include('./components/header.php');?>
    <link rel="stylesheet" href="./assets/css/students_modal.css">

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="./assets/img/images.jfif" alt="AdminLTELogo" height="180" width="180">
            </div>
            
            <!-- Navbar component -->
            <?php include('./components/navbar.php');?>
            <!-- Sidebar component -->
            <?php include('./components/student_sidebar.php');?>


            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Personal Record</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Student Information</li>
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
                                <div class="d-flex justify-content-end">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="search" class="form-control" placeholder="Search your specific record" id="studentSearch">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <table id="personalRecordTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>LRN</th>
                                            <th>Name</th>
                                            <th class="text-center">Gender</th>
                                            <th class="text-center">Grade</th>
                                            <th class="text-center">Section</th>
                                            <th class="text-center">Barangay</th>
                                            <th>Municipality</th>
                                            <th>Province</th>
                                            <th>Contact</th>
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

            <!-- View personl details modal -->
            <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="viewDetailsModalLabel">
                                <i class="fas fa-user mr-2"></i>Full Details
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <!-- Personal Information Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4 class="border-bottom pb-2">
                                            <i class="fas fa-user mr-2"></i>Personal Information
                                        </h4>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>LRN:</strong>
                                        <p id="view_lrn" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-8">
                                        <strong>Full Name:</strong>
                                        <p id="view_full_name" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4 pt-2">
                                        <strong>Date of Birth:</strong>
                                        <p id="view_birth_date" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4 pt-2">
                                        <strong>Sex:</strong>
                                        <p id="view_sex" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4 pt-2">
                                        <strong>Religion:</strong>
                                        <p id="view_religion" class="text-muted"></p>
                                    </div>
                                </div>

                                <!-- Address Information Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4 class="border-bottom pb-2">
                                            <i class="fas fa-map-marker-alt mr-2"></i>Address Information
                                        </h4>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>Complete Address:</strong>
                                        <p id="view_complete_address" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Street:</strong>
                                        <p id="view_street" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Barangay:</strong>
                                        <p id="view_barangay" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Municipality:</strong>
                                        <p id="view_municipality" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4 pt-2">
                                        <strong>Province:</strong>
                                        <p id="view_province" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4 pt-2">
                                        <strong>Contact Number:</strong>
                                        <p id="view_contact_number" class="text-muted"></p>
                                    </div>
                                </div>

                                <!-- Parent/Guardian Information Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4 class="border-bottom pb-2">
                                            <i class="fas fa-users mr-2"></i>Parent/Guardian Information
                                        </h4>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Father's Name:</strong>
                                        <p id="view_father_name" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Mother's Name:</strong>
                                        <p id="view_mother_name" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-6 pt-2">
                                        <strong>Guardian's Name:</strong>
                                        <p id="view_guardian_name" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-3 pt-2">
                                        <strong>Relationship:</strong>
                                        <p id="view_relationship" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-3 pt-2">
                                        <strong>Guardian/Parent Contact:</strong>
                                        <p id="view_guardian_contact" class="text-muted"></p>
                                    </div>
                                </div>

                                <!-- Academic Information Section -->
                                <div class="row mb-3">
                                    <div class="col-12 pt-2">
                                        <h4 class="border-bottom pb-2">
                                            <i class="fas fa-graduation-cap mr-2"></i>Academic Information
                                        </h4>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Grade Level:</strong>
                                        <p id="view_grade" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Section:</strong>
                                        <p id="view_section" class="text-muted"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Learning Modality:</strong>
                                        <p id="view_learning_modality" class="text-muted"></p>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <strong>Remarks:</strong>
                                        <p id="view_remarks" class="text-muted"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer component -->
            <?php include('./components/footer.php');?>
        </div>
        
        <?php include('./components/scripts.php');?>

        <script src="./assets/js/my_record.js"></script>
    </body>
</html>
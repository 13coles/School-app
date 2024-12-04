<?php
    session_start();

    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);
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
            <?php include('../components/teacher_sidebar.php');?>

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
                                <div class="d-flex justify-content-end align-items-center">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStudentRecordModal">
                                        <i class="fas fa-plus-circle mr-1"></i> Add record
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <table id="studentTable" class="table table-bordered table-striped">
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

            <!-- Add Student Info Modal -->
            <div class="modal fade" id="addStudentRecordModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="false">
                <div class="modal-dialog modal-dialog-scrollable modal-xl" style="max-width: 1200px;" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title font-weight-bold" id="addUserModalLabel">
                                <i class="fas fa-user-plus mr-2"></i>Student Information Form
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <form id="addStudentRecordForm" method="POST" enctype="multipart/form-data">
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
                                            <div class="form-group">
                                                <label for="lrn">Learner Reference Number (LRN) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="lrn" name="lrn" 
                                                    placeholder="Enter 12-digit LRN" required pattern="\d{12}">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="">Full Name <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col">
                                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                                            placeholder="Last Name" required>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                                            placeholder="First Name" required>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                                            placeholder="Middle Name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="birth_date">Date of Birth</label>
                                                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="gender">Sex</label>
                                                <select class="form-control" id="gender" name="sex" required>
                                                    <option value="">Select Sex</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="religion">Religion</label>
                                                <input type="text" class="form-control" id="religion" name="religion" 
                                                    placeholder="Enter Religion">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Information Section -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h4 class="border-bottom pb-2">
                                                <i class="fas fa-map-marker-alt mr-2"></i>Address Information
                                            </h4>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="street">Street Address</label>
                                                <input type="text" class="form-control" id="street" name="street" 
                                                    placeholder="Enter Street Address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="barangay">Barangay</label>
                                                <input type="text" class="form-control" id="barangay" name="barangay" 
                                                    placeholder="Enter Barangay" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="municipality">Municipality</label>
                                                <input type="text" class="form-control" id="municipality" name="municipality" 
                                                    placeholder="Enter Municipality" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="province">Province</label>
                                                <input type="text" class="form-control" id="province" name="province" 
                                                    placeholder="Enter Province" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number</label>
                                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                                    placeholder="Enter Contact Number" pattern="[0-9]{10,11}">
                                            </div>
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
                                            <div class="form-group">
                                                <label for="father_name">Father's Name</label>
                                                <input type="text" class="form-control" id="father_name" name="father_name" 
                                                    placeholder="Enter Father's Full Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mother_name">Mother's Name</label>
                                                <input type="text" class="form-control" id="mother_name" name="mother_name" 
                                                    placeholder="Enter Mother's Full Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="guardian_name">Guardian's Name</label>
                                                <input type="text" class="form-control" id="guardian_name" name="guardian_name" 
                                                    placeholder="Enter Guardian's Full Name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <label for="guardian_relationship">Relationship</label>
                                                <input type="text" class="form-control" id="guardian_relationship" name="relationship" 
                                                    placeholder="e.g., Aunt, Uncle">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="guardian_contact">Guardian's Contact</label>
                                                <input type="tel" class="form-control" id="guardian_contact" name="guardian_contact" 
                                                    placeholder="Contact Number" pattern="[0-9]{10,11}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Information Section -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h4 class="border-bottom pb-2">
                                                <i class="fas fa-graduation-cap mr-2"></i>Academic Information
                                            </h4>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="grade">Grade Level</label>
                                                <select class="form-control" id="grade" name="grade" required>
                                                    <option value="">Select Grade Level</option>
                                                    <option value="Grade 7">Grade 7</option>
                                                    <option value="Grade 8">Grade 8</option>
                                                    <option value="Grade 9">Grade 9</option>
                                                    <option value="Grade 10">Grade 10</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="section">Section</label>
                                                <input type="text" class="form-control" id="section" name="section" 
                                                    placeholder="Enter Section Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="learning_modality">Learning Modality</label>
                                                <select class="form-control" id="learning_modality" name="learning_modality" required>
                                                    <option value="">Select Learning Modality</option>
                                                    <option value="Face-to-Face">Face-to-Face</option>
                                                    <option value="Online">Online</option>
                                                    <option value="Modular">Modular</option>
                                                    <option value="Blended">Blended</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="remarks">Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" 
                                                    rows="3" placeholder="Enter any additional information or remarks"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Create Student Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Student Modal -->
            <div class="modal fade" id="viewStudentRecordModal" tabindex="-1" role="dialog" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="viewStudentModalLabel">
                                <i class="fas fa-user mr-2"></i>Student Details
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

            <!-- Edit Student modal -->
            <div class="modal fade" id="editStudentRecordModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel">
                <div class="modal-dialog modal-dialog-scrollable modal-xl" style="max-width: 1200px;" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title font-weight-bold" id="editStudentModalLabel">
                                <i class="fas fa-user-plus mr-2"></i>Student Information Form
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <form id="editStudentRecord" method="POST" enctype="multipart/form-data">
                            <!-- Hidden input that will take the student id selected -->
                             <input type="hidden" name="edit_student_id" id="edit_student_id">
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
                                            <div class="form-group">
                                                <label for="edit_lrn">Learner Reference Number (LRN) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_lrn" name="lrn" 
                                                    placeholder="Enter 12-digit LRN" required pattern="\d{12}">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="edit_full_name">Full Name <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col">
                                                        <input type="text" class="form-control" id="edit_last_name" name="last_name" 
                                                            placeholder="Last Name" required>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" id="edit_first_name" name="first_name" 
                                                            placeholder="First Name" required>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" id="edit_middle_name" name="middle_name" 
                                                            placeholder="Middle Name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_birth_date">Date of Birth</label>
                                                <input type="date" class="form-control" id="edit_birth_date" name="birth_date" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_sex">Sex</label>
                                                <select class="form-control" id="edit_sex" name="sex" required>
                                                    <option value="">Select Sex</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_religion">Religion</label>
                                                <input type="text" class="form-control" id="edit_religion" name="religion" 
                                                    placeholder="Enter Religion">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Information Section -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h4 class="border-bottom pb-2">
                                                <i class="fas fa-map-marker-alt mr-2"></i>Address Information
                                            </h4>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_street">Street Address</label>
                                                <input type="text" class="form-control" id="edit_street" name="street" 
                                                    placeholder="Enter Street Address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_barangay">Barangay</label>
                                                <input type="text" class="form-control" id="edit_barangay" name="barangay" 
                                                    placeholder="Enter Barangay" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_municipality">Municipality</label>
                                                <input type="text" class="form-control" id="edit_municipality" name="municipality" 
                                                    placeholder="Enter Municipality" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_province">Province</label>
                                                <input type="text" class="form-control" id="edit_province" name="province" 
                                                    placeholder="Enter Province" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_contact_number">Contact Number</label>
                                                <input type="tel" class="form-control" id="edit_contact_number" name="contact_number" 
                                                    placeholder="Enter Contact Number" pattern="[0-9]{10,11}">
                                            </div>
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
                                            <div class="form-group">
                                                <label for="edit_father_name">Father's Name</label>
                                                <input type="text" class="form-control" id="edit_father_name" name="father_name" 
                                                    placeholder="Enter Father's Full Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_mother_name">Mother's Name</label>
                                                <input type="text" class="form-control" id="edit_mother_name" name="mother_name" 
                                                    placeholder="Enter Mother's Full Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="guardian_name">Guardian's Name</label>
                                                <input type="text" class="form-control" id="edit_guardian_name" name="guardian_name" 
                                                    placeholder="Enter Guardian's Full Name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <label for="edit_guardian_relationship">Relationship</label>
                                                <input type="text" class="form-control" id="edit_guardian_relationship" name="relationship" 
                                                    placeholder="e.g., Aunt, Uncle">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="edit_guardian_contact">Guardian's Contact</label>
                                                <input type="tel" class="form-control" id="edit_guardian_contact" name="guardian_contact" 
                                                    placeholder="Contact Number" pattern="[0-9]{10,11}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Information Section -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h4 class="border-bottom pb-2">
                                                <i class="fas fa-graduation-cap mr-2"></i>Academic Information
                                            </h4>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_grade">Grade Level</label>
                                                <select class="form-control" id="edit_grade" name="grade" required>
                                                    <option value="">Select Grade Level</option>
                                                    <option value="Grade 7">Grade 7</option>
                                                    <option value="Grade 8">Grade 8</option>
                                                    <option value="Grade 9">Grade 9</option>
                                                    <option value="Grade 10">Grade 10</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_section">Section</label>
                                                <input type="text" class="form-control" id="edit_section" name="section" 
                                                    placeholder="Enter Section Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_learning_modality">Learning Modality</label>
                                                <select class="form-control" id="edit_learning_modality" name="learning_modality" required>
                                                    <option value="">Select Learning Modality</option>
                                                    <option value="Face-to-Face">Face-to-Face</option>
                                                    <option value="Online">Online</option>
                                                    <option value="Modular">Modular</option>
                                                    <option value="Blended">Blended</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="edit_remarks">Remarks</label>
                                                <textarea class="form-control" id="edit_remarks" name="remarks" 
                                                    rows="3" placeholder="Enter any additional information or remarks"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Update Record
                                </button>
                            </div>
                        </form>
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
        <script src="../assets/js/student_record.js"></script>
    </body>
</html>
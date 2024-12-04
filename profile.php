<?php 
    session_start();

    require_once('utils/access_control.php');
    require_once('utils/db_connection.php');

    checkAccess(['student']);

    // Fetch student details
    try {
        $query = $pdo->prepare("SELECT * FROM students WHERE id = :student_id");
        $query->execute(['student_id' => $_SESSION['student_id']]);
        $studentDetails = $query->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        die("Error fetching student details: " . $e->getMessage());
    }
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
            <?php include('./components/student_sidebar.php');?>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">My Profile</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">My Profile</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                                <!-- Profile Image -->
                                <div class="card card-primary card-outline">
                                    <div class="card-body box-profile">
                                        <div class="text-center">
                                            <img class="profile-user-img img-fluid img-circle" 
                                                src="./assets/img/user8-128x128.jpg" 
                                                alt="User profile picture">
                                        </div>
                                        <h3 class="profile-username text-center"><?php echo htmlspecialchars($studentDetails['full_name']); ?></h3>
                                        <p class="text-muted text-center">
                                            <?php echo htmlspecialchars($studentDetails['grade'] . ' - ' . $studentDetails['section']); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Additional Details Card -->
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Quick Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <strong><i class="fas fa-book mr-1"></i> LRN</strong>
                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['lrn']); ?></p>
                                        <hr class="pt-2">
                                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                        <p class="text-muted pb-2">
                                            <?php 
                                            echo htmlspecialchars(implode(', ', array_filter([
                                                $studentDetails['street'], 
                                                $studentDetails['barangay'], 
                                                $studentDetails['municipality'], 
                                                $studentDetails['province']
                                            ])));
                                            ?>
                                        </p>
                                        <hr class="pt-2">
                                        <strong><i class="fas fa-phone mr-1"></i> Contact</strong>
                                        <p class="text-muted"><?php echo htmlspecialchars($studentDetails['contact_number']); ?></p>
                                    </div>
                                </div>

                                <!-- Account Settings Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <a href="./settings.php" class="card-title font-semibold">
                                            <i class="fas fa-cogs mr-2"></i>Account Settings
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <ul class="nav nav-pills">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#personal" data-toggle="tab">
                                                    Personal Information
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#academic" data-toggle="tab">
                                                    Academic Information
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#guardian" data-toggle="tab">
                                                    Guardian Information
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#edit" data-toggle="tab">
                                                    Edit Profile
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- Personal Information Tab -->
                                            <div class="active tab-pane" id="personal">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Full Name</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['full_name']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Date of Birth</strong>
                                                        <p class="text-muted pb-2">
                                                            <?php 
                                                            $birthDate = new DateTime($studentDetails['birth_date']);
                                                            echo $birthDate->format('F j, Y') . ' (' . 
                                                                 $birthDate->diff(new DateTime())->y . ' years old)'; 
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Gender</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['sex']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Religion</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['religion']); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Academic Information Tab -->
                                            <div class="tab-pane" id="academic">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Grade Level</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['grade']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Section</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['section']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Learning Modality</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['learning_modality']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Remarks</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['remarks'] ?? 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Guardian Information Tab -->
                                            <div class="tab-pane" id="guardian">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Father's Name</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['father_name'] ?? 'N/A'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Mother's Name</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['mother_name'] ?? 'N/A'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Guardian's Name</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['guardian_name'] ?? 'N/A'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Relationship</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['relationship'] ?? 'N/A'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Guardian Contact</strong>
                                                        <p class="text-muted pb-2"><?php echo htmlspecialchars($studentDetails['guardian_contact'] ?? 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Profile Tab -->
                                            <div class="tab-pane" id="edit">
                                                <form id="editProfileForm">
                                                    <!-- Personal Information Section -->
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <h4 class="border-bottom pb-2">
                                                                <i class="fas fa-user mr-2"></i>Personal Information
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>LRN (Learner Reference Number)</label>
                                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($studentDetails['lrn']); ?>" readonly>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <label>Full Name</label>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <input type="text" class="form-control" name="last_name" 
                                                                        placeholder="Last Name" 
                                                                        value="<?php 
                                                                        $nameParts = explode(' ', $studentDetails['full_name']);
                                                                        echo htmlspecialchars(end($nameParts)); ?>" readonly>
                                                                </div>
                                                                <div class="col">
                                                                    <input type="text" class="form-control" name="first_name" 
                                                                        placeholder="First Name" 
                                                                        value="<?php 
                                                                        $nameParts = explode(' ', $studentDetails['full_name']);
                                                                        echo htmlspecialchars($nameParts[0]); ?>" readonly>
                                                                </div>
                                                                <div class="col">
                                                                    <input type="text" class="form-control" name="middle_name" 
                                                                        placeholder="Middle Name" 
                                                                        value="<?php 
                                                                        $nameParts = explode(' ', $studentDetails['full_name']);
                                                                        echo count($nameParts) > 2 ? htmlspecialchars($nameParts[1]) : ''; ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Date of Birth</label>
                                                            <input type="date" class="form-control" name="birth_date" 
                                                                value="<?php echo htmlspecialchars($studentDetails['birth_date']); ?>">
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Sex</label>
                                                            <input type="text" class="form-control" name="sex" 
                                                                value="<?php echo htmlspecialchars($studentDetails['sex']); ?>" readonly>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Religion</label>
                                                            <input type="text" class="form-control" name="religion" 
                                                                value="<?php echo htmlspecialchars($studentDetails['religion']); ?>">
                                                        </div>
                                                    </div>

                                                    <!-- Address Information Section -->
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <h4 class="border-bottom pb-2">
                                                                <i class="fas fa-map-marker-alt mr-2"></i>Address Information
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label>Street Address</label>
                                                            <input type="text" class="form-control" name="street" 
                                                                value="<?php echo htmlspecialchars($studentDetails['street']); ?>">
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label>Barangay</label>
                                                            <input type="text" class="form-control" name="barangay" 
                                                                value="<?php echo htmlspecialchars($studentDetails['barangay']); ?>" required>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Municipality</label>
                                                            <input type="text" class="form-control" name="municipality" 
                                                                value="<?php echo htmlspecialchars($studentDetails['municipality']); ?>" required>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Province</label>
                                                            <input type="text" class="form-control" name="province" 
                                                                value="<?php echo htmlspecialchars($studentDetails['province']); ?>" required>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Contact Number</label>
                                                            <input type="tel" class="form-control" name="contact_number" 
                                                                value="<?php echo htmlspecialchars($studentDetails['contact_number']); ?>" 
                                                                pattern="[0-9]{10,11}">
                                                        </div>
                                                    </div>

                                                    <!-- Parent/Guardian Information Section -->
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <h4 class="border-bottom pb-2">
                                                                <i class="fas fa-users mr-2"></i>Parent/Guardian Information
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label>Father's Name</label>
                                                            <input type="text" class="form-control" name="father_name" 
                                                                value="<?php echo htmlspecialchars($studentDetails['father_name'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label>Mother's Name</label>
                                                            <input type="text" class="form-control" name="mother_name" 
                                                                value="<?php echo htmlspecialchars($studentDetails['mother_name'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label>Guardian's Name</label>
                                                            <input type="text" class="form-control" name="guardian_name" 
                                                                value="<?php echo htmlspecialchars($studentDetails['guardian_name'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label>Relationship</label>
                                                            <input type="text" class="form-control" name="relationship" 
                                                                value="<?php echo htmlspecialchars($studentDetails['relationship'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label>Guardian's Contact</label>
                                                            <input type="tel" class="form-control" name="guardian_contact" 
                                                                value="<?php echo htmlspecialchars($studentDetails['guardian_contact'] ?? ''); ?>" 
                                                                pattern="[0-9]{10,11}">
                                                        </div>
                                                    </div>

                                                    <!-- Academic Information Section -->
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <h4 class="border-bottom pb-2">
                                                                <i class="fas fa-graduation-cap mr-2"></i>Academic Information
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Grade Level</label>
                                                            <input type="text" class="form-control" name="grade" 
                                                                value="<?php echo htmlspecialchars($studentDetails['grade']); ?>" readonly>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Section</label>
                                                            <input type="text" class="form-control" name="section" 
                                                                value="<?php echo htmlspecialchars($studentDetails['section']); ?>" readonly>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Learning Modality</label>
                                                            <input type="text" class="form-control" name="learning_modality" 
                                                                value="<?php echo htmlspecialchars($studentDetails['learning_modality']); ?>" readonly>
                                                        </div>
                                                        <div class="col-12 form-group">
                                                            <label>Remarks</label>
                                                            <textarea class="form-control" name="remarks" rows="3"
                                                                placeholder="Enter any additional information or remarks" readonly>
                                                                <?php 
                                                                    echo htmlspecialchars($studentDetails['remarks'] ?? '');
                                                                ?>
                                                            </textarea>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save mr-1"></i>Update Profile
                                                        </button>
                                                    </div>
                                                </form>
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
            <?php include('./components/footer.php');?>
        </div>
        
        <?php include('./components/scripts.php');?>

        <script src="./assets/js/profile.js"></script>
    </body>
</html>
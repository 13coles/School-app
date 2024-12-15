<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['admin']);

    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        $stmt = $pdo->prepare("SELECT full_name, grade, section FROM students WHERE id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        $student = $stmt->fetch();
    
        if (!$student) {
            die("Student not found.");
        }
        $stmt = $pdo->prepare("
            SELECT s.subject_name, ss.subject_id
            FROM student_subject ss
            JOIN subjects s ON ss.subject_id = s.id
            WHERE ss.student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $subjects = $stmt->fetchAll();
        $stmt = $pdo->prepare("
            SELECT * FROM student_card WHERE student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $student_card = $stmt->fetch();
    
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
                                <h1 class="m-0">Student Report Card</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="students.php">Students Table</a></li>
                                    <li class="breadcrumb-item active">Student Report Card</li>
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
                            <a href="print-grade.php?student_id=<?php echo $student_id; ?>" class="btn btn-secondary float-right">
                                <i class="fas fa-print mr-1"></i> Print
                            </a>

                            </div>
                            <div class="card-body">
                           
                            <table id="gradesTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle bg-primary text-center">Subjects</th>
                                            <th colspan="8" class="text-center bg-primary">
                                                <?php echo $student['full_name'] . " - Grade: " . $student['grade'] . " Section: " . $student['section']; ?>
                                            </th>

                                            <th rowspan="2" class="align-middle bg-primary text-center">Final Grade</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center bg-warning">1st Quarter</th>
                                            <th colspan="2" class="text-center bg-warning">2nd Quarter</th>
                                            <th colspan="2" class="text-center bg-warning">3rd Quarter</th>
                                            <th colspan="2" class="text-center bg-warning">4th Quarter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                       
                                        $total_final_grades = 0;
                                        $subject_count = 0;
                                        ?>

                                        <?php foreach ($subjects as $subject): ?>
                                            <tr>
                                                <td><?php echo $subject['subject_name']; ?></td>
                                                <td colspan="2" class="p-1">
                                                    <input type="text" class="form-control" value="<?php echo $student_card["1st_quarter"] ?? ''; ?>" readonly>
                                                </td>
                                                <td colspan="2" class="p-1">
                                                    <input type="text" class="form-control" value="<?php echo $student_card["2nd_quarter"] ?? ''; ?>" readonly>
                                                </td>
                                                <td colspan="2" class="p-1">
                                                    <input type="text" class="form-control" value="<?php echo $student_card["3rd_quarter"] ?? ''; ?>" readonly>
                                                </td>
                                                <td colspan="2" class="p-1">
                                                    <input type="text" class="form-control" value="<?php echo $student_card["4th_quarter"] ?? ''; ?>" readonly>
                                                </td>

                                                <?php 
                                                    // Calculate the final grade for this subject
                                                    $first_quarter = $student_card["1st_quarter"] ?? 0;
                                                    $second_quarter = $student_card["2nd_quarter"] ?? 0;
                                                    $third_quarter = $student_card["3rd_quarter"] ?? 0;
                                                    $fourth_quarter = $student_card["4th_quarter"] ?? 0;
                                                    $final_grade = ($first_quarter + $second_quarter + $third_quarter + $fourth_quarter) / 4;
                                                    $final_grade_rounded = round($final_grade);

                                                    // Add to total final grades and count subjects for general average
                                                    $total_final_grades += $final_grade_rounded;
                                                    $subject_count++;
                                                ?>
                                                <td colspan="2" class="p-1">
                                                    <input type="text" class="form-control" value="<?php echo $final_grade_rounded; ?>" readonly>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php 
                                            // Calculate the general average
                                            if ($subject_count > 0) {
                                                $general_avg = $total_final_grades / $subject_count;
                                                $general_avg_rounded = round($general_avg);
                                            } else {
                                                $general_avg_rounded = 0; 
                                            }
                                        ?>
                                        <tr>
                                            <td colspan="8" class="text-right bg-primary"><strong>General Average</strong></td>
                                            <td colspan="2" class="p-1 bg-primary">
                                                <input type="text" class="form-control" value="<?php echo $general_avg_rounded; ?>" readonly>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            <?php
                        } else {
                            echo "Student ID not provided.";
                        }
                        ?>
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
<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);

    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        $stmt = $pdo->prepare("SELECT full_name, grade, section FROM students WHERE id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        $student = $stmt->fetch();
    
        if (!$student) {
            die("Student not found.");
        }

        // Fetch the subjects for the student
        $stmt = $pdo->prepare("
            SELECT s.subject_name, ss.subject_id
            FROM student_subject ss
            JOIN subjects s ON ss.subject_id = s.id
            WHERE ss.student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $subjects = $stmt->fetchAll();
        
        // Fetch all grades and status for the student
        $stmt = $pdo->prepare("
            SELECT * FROM student_card WHERE student_id = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
        $student_cards = $stmt->fetchAll();
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
                                <h1 class="m-0">Student Report Card</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="student_record.php">Students Table</a></li>
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
                            <a href="tc_printGrade.php?student_id=<?php echo $student_id; ?>" class="btn btn-primary float-right">
                                <i class="fas fa-print mr-1"></i> Print
                            </a>

                            </div>
                            <div class="card-body">
                            <table id="gradesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="12"><strong>Student Name: </strong><?php echo $student['full_name'] . " - Grade: " . $student['grade'] . " Section: " . $student['section']; ?></th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2" class="align-middle  text-center">Learning Areas</th>
                                        <th colspan="8" class="text-center ">
                                            Quarter's
                                        </th>
                                        <th rowspan="2" class="align-middle  text-center">Final Grade</th>
                                        <th rowspan="2" class="align-middle  text-center">Remarks</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">1st Quarter</th>
                                        <th colspan="2" class="text-center">2nd Quarter</th>
                                        <th colspan="2" class="text-center">3rd Quarter</th>
                                        <th colspan="2" class="text-center">4th Quarter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_final_grades = 0;
                                        $subject_count = 0;
                                        foreach ($subjects as $subject):
                                            // Get the grades and status for this subject
                                            $grades = array_filter($student_cards, function($card) use ($subject) {
                                                return $card['subject_id'] == $subject['subject_id'];
                                            });

                                            $grade = reset($grades); // Take the first (and only) grade for the subject
                                    ?>
                                        <tr>
                                            <td><?php echo $subject['subject_name']; ?></td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["1st_quarter"] ?? ''; ?>
                                            </td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["2nd_quarter"] ?? ''; ?>
                                            </td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["3rd_quarter"] ?? ''; ?>
                                            </td>
                                            <td colspan="2" class="p-1 text-center">
                                                <?php echo $grade["4th_quarter"] ?? ''; ?>
                                            </td>

                                            <?php
                                                $first_quarter = $grade["1st_quarter"] ?? 0;
                                                $second_quarter = $grade["2nd_quarter"] ?? 0;
                                                $third_quarter = $grade["3rd_quarter"] ?? 0;
                                                $fourth_quarter = $grade["4th_quarter"] ?? 0;
                                                $final_grade = ($first_quarter + $second_quarter + $third_quarter + $fourth_quarter) / 4;
                                                $final_grade_rounded = round($final_grade);

                                                $total_final_grades += $final_grade_rounded;
                                                $subject_count++;
                                            ?>
                                            <td class="p-1 text-center">
                                                <?php echo $final_grade_rounded; ?>
                                            </td>
                                            <td class="p-1 text-center">
                                                <?php echo $grade['status'] ?? ''; ?>
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
                                        <td colspan="8" class="text-right "><strong>General Average:</strong></td>
                                        <td colspan="3" class="p-1  text-center">
                                            <?php echo $general_avg_rounded; ?>
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
                <!-- main end -->
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

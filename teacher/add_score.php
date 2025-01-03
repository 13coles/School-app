<?php
    session_start();

    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);

    $teacher_id = $_SESSION['teacher_id']; 
   
    $query = "
    SELECT DISTINCT ts.student_id, s.full_name
    FROM teacher_student_assignments ts
    JOIN students s ON ts.student_id = s.id
    WHERE ts.teacher_id = :teacher_id
    ";  
  
    $stmt = $pdo->prepare($query);
    $stmt->execute(['teacher_id' => $teacher_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT id, subject_name FROM subjects";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                <h1 class="m-0">Add Score</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Add score</li>
                                </ol>
                            </div>
                           
                        </div>
                        <a href="class_records.php" class="btn btn-primary">Back</a>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                    <?php include '../utils/sessions.php' ?>
                        <div class="card">
                            <div class="card-body">
                            <form action="submit_score.php" method="POST">
                                <table id="studentTable" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <!-- Learner selection, quarter, and subject -->
                                            <th colspan="4">
                                                <label for="student_id">Learner:</label>
                                                <select name="student_id" id="student_id" class="form-control">
                                                    <!-- Dynamically populate the options -->
                                                    <?php foreach ($students as $student): ?>
                                                        <option value="<?= htmlspecialchars($student['student_id']); ?>">
                                                            <?= htmlspecialchars($student['full_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </th>
                                            <th colspan="4">
                                                <label for="quarter">Quarter:</label>
                                                <select name="quarter" id="quarter" class="form-control">
                                                    <option value="1">1st Quarter</option>
                                                    <option value="2">2nd Quarter</option>
                                                    <option value="3">3rd Quarter</option>
                                                    <option value="4">4th Quarter</option>
                                                </select>
                                            </th>
                                            <th colspan="5">
                                                <label for="subject_id">Subject:</label>
                                                <select name="subject_id" id="subject_id" class="form-control">
                                                    <!-- Dynamically populate the subject options from the database -->
                                                    <?php foreach ($subjects as $subject): ?>
                                                        <option value="<?= htmlspecialchars($subject['id']); ?>">
                                                            <?= htmlspecialchars($subject['subject_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">Written Works (30%)</th>
                                            <th colspan="4">Performance Tasks (50%)</th>
                                            <th colspan="2">Exams (20%)</th>
                                        </tr>
                                        <tr>
                                            <!-- WW -->
                                            <th>WW 1</th>
                                            <th>WW 2</th>
                                            <th>WW 3</th>
                                            <th>WW 4</th>
                                            <!-- PT -->
                                            <th>PT 1</th>
                                            <th>PT 2</th>
                                            <th>PT 3</th>
                                            <th>PT 4</th>
                                            <!-- Exam -->
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <!-- Written Works -->
                                            <td><input type="number" name="ww_1" class="form-control" min="0"  max="25" required></td>
                                            <td><input type="number" name="ww_2" class="form-control" min="0"  max="25" required></td>
                                            <td><input type="number" name="ww_3" class="form-control" min="0"  max="25" required></td>
                                            <td><input type="number" name="ww_4" class="form-control" min="0"  max="25" required></td>
                                            <!-- Performance Tasks -->
                                            <td><input type="number" name="pt_1" class="form-control" min="0"  max="50" required></td>
                                            <td><input type="number" name="pt_2" class="form-control" min="0"  max="50" required></td>
                                            <td><input type="number" name="pt_3" class="form-control" min="0"  max="50" required></td>
                                            <td><input type="number" name="pt_4" class="form-control" min="0"  max="50" required></td>
                                            <!-- Exams -->
                                            <td><input type="number" name="exam_1" class="form-control" min="0"  max="50" required></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary">Submit Scores</button>
                            </form>


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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputs = document.querySelectorAll('#studentTable input[type="number"]');

                inputs.forEach(input => {
                    input.addEventListener('input', function () {
                        const max = parseInt(this.max, 10);
                        const min = parseInt(this.min, 10);
                        const value = parseInt(this.value, 10) || 0; 

                        if (value > max) {
                            this.value = max; 
                        } else if (value < min) {
                            this.value = min; 
                        }
                    });

                    input.addEventListener('keydown', function (event) {
                        const max = parseInt(this.max, 10);
                        const min = parseInt(this.min, 10);
                        const value = parseInt(this.value, 10) || 0;

                        if ((event.key === 'ArrowUp' && value >= max) || 
                            (event.key === 'ArrowDown' && value <= min)) {
                            event.preventDefault();
                        }
                    });
                });
            });
        </script>


    </body>
</html>
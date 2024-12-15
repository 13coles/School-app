<?php 
    session_start();

    require_once('../utils/db_connection.php');
    require_once('../utils/access_control.php');
    
    checkAccess(['admin']);
    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        $stmtStudent = $pdo->prepare("SELECT * FROM students WHERE id = :student_id");
        $stmtStudent->execute(['student_id' => $student_id]);
        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);
        $stmtSubjects = $pdo->prepare("
            SELECT subjects.id, subjects.subject_name 
            FROM student_subject 
            JOIN subjects ON student_subject.subject_id = subjects.id 
            WHERE student_subject.student_id = :student_id
        ");
        $stmtSubjects->execute(['student_id' => $student_id]);
        $subjects = $stmtSubjects->fetchAll(PDO::FETCH_ASSOC);
    }
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
            .custom-heading {
                font-size: 24px;
                font-weight: bold;
                color: #fff;
                background-color: #007bff;
                border: 2px solid #0056b3; 
                padding: 10px;
                margin-bottom: 20px;
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
                                <h1 class="m-0">Add Grade to this Student Name</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="students.php">Student Table</a></li>
                                    <li class="breadcrumb-item active">ADD GRADE</li>
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
                            <div class="card-header ">
                                <h2 class="custom-heading text-center">
                                    <?php echo htmlspecialchars($student['full_name']); ?>
                                </h2>
                            </div>
                            <div class="card-body">
                             

                                <form method="POST" action="save_grades.php">
                                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                                    <div class="form-group">
                                        <label for="quarter">Select Quarter:</label>
                                        <select name="quarter" id="quarter" class="form-control" required>
                                            <option value="1">1st Quarter</option>
                                            <option value="2">2nd Quarter</option>
                                            <option value="3">3rd Quarter</option>
                                            <option value="4">4th Quarter</option>
                                        </select>
                                    </div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Written Test (30%)</th>
                                                <th>Performance Task (50%)</th>
                                                <th>Exam (20%)</th>
                                                <th>Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subjects as $subject): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                                    <td>
                                                        <input type="number" name="grades[<?php echo $subject['id']; ?>][written_test]" 
                                                            class="form-control grade-input" min="0" max="30" required
                                                            data-subject-id="<?php echo $subject['id']; ?>" data-max="30">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="grades[<?php echo $subject['id']; ?>][performance_task]" 
                                                            class="form-control grade-input" min="0" max="50" required
                                                            data-subject-id="<?php echo $subject['id']; ?>" data-max="50">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="grades[<?php echo $subject['id']; ?>][exm]" 
                                                            class="form-control grade-input" min="0" max="20" required
                                                            data-subject-id="<?php echo $subject['id']; ?>" data-max="20">
                                                    </td>
                                                    <td>
                                                        <span id="grade-<?php echo $subject['id']; ?>">0</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <button type="submit" class="btn btn-primary float-right">Save Grades</button>
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
        
        <!-- DataTables & Plugins -->
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.grade-input').forEach(function (input) {
                    input.addEventListener('input', function () {
                        const subjectId = input.dataset.subjectId;
                        const maxAllowed = parseFloat(input.dataset.max);
                        if (parseFloat(input.value) > maxAllowed) {
                            input.value = maxAllowed; 
                        }
                        const writtenTest = parseFloat(document.querySelector(`input[name="grades[${subjectId}][written_test]"]`).value) || 0;
                        const performanceTask = parseFloat(document.querySelector(`input[name="grades[${subjectId}][performance_task]"]`).value) || 0;
                        const exm = parseFloat(document.querySelector(`input[name="grades[${subjectId}][exm]"]`).value) || 0;
                        const totalGrade = writtenTest + performanceTask + exm;
                        document.getElementById(`grade-${subjectId}`).innerText = totalGrade.toFixed(2);
                    });
                });
            });
        </script>


    </body>
</html>
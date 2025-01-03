<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['teacher']);
    // Get teacher_id from session
    $teacher_id = $_SESSION['teacher_id'];

    // Get the quarter and subject filter values from GET request (if set)
    $quarter = isset($_GET['quarter']) ? $_GET['quarter'] : '';
    $subject = isset($_GET['subject']) ? $_GET['subject'] : '';

    // Fetch class records for the logged-in teacher, applying filters if set
    $query = "
        SELECT 
            s.full_name,
            s.section,
            s.grade,
            sub.subject_name,
            cr.quarter,
            cr.ww_1, cr.ww_2, cr.ww_3, cr.ww_4, cr.ww_total,
            (cr.ww_1 + cr.ww_2 + cr.ww_3 + cr.ww_4) AS ww_score,
            cr.pt_1, cr.pt_2, cr.pt_3, cr.pt_4, cr.pt_total,
            (cr.pt_1 + cr.pt_2 + cr.pt_3 + cr.pt_4) AS pt_score,
            cr.exam_1, cr.exam_total,
            (cr.ww_total + cr.pt_total + cr.exam_total) AS final_grade
            
        FROM 
            class_record cr
        JOIN 
            students s ON cr.student_id = s.id
        JOIN 
            subjects sub ON cr.subject_id = sub.id
        WHERE
            cr.teacher_id = :teacher_id
            " . ($quarter ? " AND cr.quarter = :quarter" : "") . "
            " . ($subject ? " AND sub.subject_name = :subject" : "") . "
        ORDER BY 
            s.full_name
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
    if ($quarter) {
        $stmt->bindParam(':quarter', $quarter, PDO::PARAM_STR);
    }
    if ($subject) {
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
    }
    $stmt->execute();
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
        <link rel="stylesheet" href="../assets/css/students_modal.css">
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
                            <div class="col-sm-6 mb-2">
                                <h1 class="m-0">Class Records</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Class records</li>
                                </ol>
                            </div>
                        </div>
                        <a href="add_score.php" class="btn btn-primary">ADD SCORE</a>
                    </div>
                </div>

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="col-sm-6 m-3">
                                    <form method="GET" action="">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="quarter">Select Quarter:</label>
                                                <select name="quarter" id="quarter" class="form-control">
                                                    <option value="">All Quarters</option>
                                                    <option value="1" <?= $quarter == '1' ? 'selected' : '' ?>>Quarter 1</option>
                                                    <option value="2" <?= $quarter == '2' ? 'selected' : '' ?>>Quarter 2</option>
                                                    <option value="3" <?= $quarter == '3' ? 'selected' : '' ?>>Quarter 3</option>
                                                    <option value="4" <?= $quarter == '4' ? 'selected' : '' ?>>Quarter 4</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="subject">Select Subject:</label>
                                                <select name="subject" id="subject" class="form-control">
                                                    <option value="">All Subjects</option>
                                                    <?php
                                                        // Fetch subjects to populate the subject dropdown
                                                        $subjectQuery = "SELECT DISTINCT subject_name FROM subjects ORDER BY subject_name";
                                                        $subjectStmt = $pdo->query($subjectQuery);
                                                        while ($row = $subjectStmt->fetch(PDO::FETCH_ASSOC)) {
                                                            echo '<option value="' . htmlspecialchars($row['subject_name']) . '" ' . ($subject == $row['subject_name'] ? 'selected' : '') . '>' . htmlspecialchars($row['subject_name']) . '</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4 mt-2">
                                                <button type="submit" class="btn btn-primary mt-4 mt-3">Filter</button>
                                            </div>
                                            <a href="print-class_record.php?quarter=<?= htmlspecialchars($quarter) ?>&subject=<?= htmlspecialchars($subject) ?>" 
                                                class="btn btn-primary float-right">Print
                                            </a>
                                        </div>
                                        
                                    </form>
                                    
                                </div>
                                
                            <div class="card-body">
                                <table id="studentTable" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class=" align-middle text-center">Learner's Name</th>
                                            <th colspan="6" class="text-center">Written Works (30%)</th>
                                            <th colspan="6" class="text-center">Performance Test (50%)</th>
                                            <th colspan="2" class="text-center"> Exams (20%)</th>
                                            <th rowspan="2" class="align-middle text-center">Final Grade</th>
                                        </tr>
                                        <tr>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>Total</th>
                                            <th>PS</th>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>Total</th>
                                            <th>PS </th>
                                            <th>Total</th>
                                            <th>PS </th>
                                        </tr>
                                        <tr class="bg-secondary">
                                        <th rowspan="2" class=" align-middle text-right">Perfect Score</th>
                                            <th>25</th>
                                            <th>25</th>
                                            <th>25</th>
                                            <th>25</th>
                                            <th>100</th>
                                            <th>100%</th>
                                            <th>50</th>
                                            <th>50</th>
                                            <th>50</th>
                                            <th>50</th>
                                            <th>200</th>
                                            <th>100%</th>
                                            <th>50</th>
                                            <th>100%</th>
                                            <th rowspan="2" class="align-middle text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['full_name']) ?>, <?= htmlspecialchars($row['grade']) ?> - <?= htmlspecialchars($row['section']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_1']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_2']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_3']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_4']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_score']) ?></td>
                                                <td><?= htmlspecialchars($row['ww_total']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_1']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_2']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_3']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_4']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_score']) ?></td>
                                                <td><?= htmlspecialchars($row['pt_total']) ?></td>
                                                <td><?= htmlspecialchars($row['exam_1']) ?></td>
                                                <td><?= htmlspecialchars($row['exam_total']) ?></td>
                                                <td><?= htmlspecialchars($row['final_grade']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
    </body>
</html>

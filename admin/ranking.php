<?php
    session_start();
    require_once '../utils/db_connection.php';
    require_once('../utils/access_control.php');
    
    checkAccess(['admin']);

    $grade = isset($_GET['grade']) ? $_GET['grade'] : null;
    $section = isset($_GET['section']) ? $_GET['section'] : null;

    $query = "
        SELECT 
            s.id,
            s.lrn,
            s.full_name,
            COUNT(sc.subject_id) AS total_subjects,  -- Count total number of subjects
            SUM((COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4) AS total_final_grades,  -- Sum of final grades
            (SUM((COALESCE(sc.1st_quarter, 0) + COALESCE(sc.2nd_quarter, 0) + COALESCE(sc.3rd_quarter, 0) + COALESCE(sc.4th_quarter, 0)) / 4) / COUNT(sc.subject_id)) AS general_avg  -- Calculate final average
        FROM 
            student_card sc
        JOIN 
            students s ON s.id = sc.student_id
        WHERE
            (:grade IS NULL OR s.grade = :grade) 
            AND (:section IS NULL OR s.section = :section)
        GROUP BY 
            s.id
        ORDER BY 
            general_avg DESC;  -- Order by the general average (highest to lowest)
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }

    // Fetch available grades and sections for filtering
    try {
        $gradesQuery = "SELECT DISTINCT grade FROM students";
        $stmt = $pdo->query($gradesQuery);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        $sectionsQuery = "SELECT DISTINCT section FROM students";
        $stmt = $pdo->query($sectionsQuery);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 
    } catch (PDOException $e) {
        die("Error fetching data for grades and sections: " . $e->getMessage());
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../components/header.php'; ?>
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
        <?php include('../components/navbar.php'); ?>
        <?php include('../components/sidebar.php'); ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Student Performance Report</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="students.php">Students Table</a></li>
                                <li class="breadcrumb-item active">Student Performance Report</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <?php include '../utils/sessions.php' ?>
                    <div class="card">
                        <div class="card-header">
                            <form method="GET" action="">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="grade">Grade</label>
                                        <select id="grade" name="grade" class="form-control">
                                            <option value="">All Grades</option>
                                            <?php foreach ($grades as $g) { ?>
                                                <option value="<?= htmlspecialchars($g['grade']) ?>" <?= $g['grade'] == $grade ? 'selected' : '' ?>><?= htmlspecialchars($g['grade']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="section">Section</label>
                                        <select id="section" name="section" class="form-control">
                                            <option value="">All Sections</option>
                                            <?php foreach ($sections as $sec) { ?>
                                                <option value="<?= htmlspecialchars($sec['section']) ?>" <?= $sec['section'] == $section ? 'selected' : '' ?>><?= htmlspecialchars($sec['section']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary mt-4 pb-2">Filter</button>
                                    </div>
                                </div>
                            </form>
                            <a href="print-ranking.php?grade=<?= urlencode($grade) ?>&section=<?= urlencode($section) ?>" class="btn btn-md btn-primary">Print</a>
                        </div>
                        <div class="card-body">
                            <table id="gradesTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle bg-primary text-center">Ranking</th>
                                       
                                        <th rowspan="2" class="align-middle bg-primary text-center">Full Name</th>
                                        <th rowspan="2" class="align-middle bg-primary text-center">Final Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rank = 1;
                                    foreach ($students as $student) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $rank++ . "</td>";
                                        echo "<td class='text-center'>" . htmlspecialchars($student['full_name']) . "</td>";
                                        echo "<td class='text-center'>" . number_format($student['general_avg'], 2) . "</td>"; 
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('../components/footer.php'); ?>
    </div>

    <!-- Scripts component -->
    <?php include('../components/scripts.php'); ?>
    
    <!-- DataTables & Plugins -->
    <script src="../vendor/almasaeed2010/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    
    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            $('#gradesTable').DataTable();
        });
    </script>
</body>
</html>

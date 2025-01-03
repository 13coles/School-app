<?php
require_once '../utils/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? null;
    $quarter = $_POST['quarter'];

    if (empty($student_id)) {
        die("Error: Student ID is missing. Please provide a valid student ID.");
    }
    if (empty($quarter)) {
        die("Error: Quarter is missing. Please select a valid quarter.");
    }
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM grades 
        WHERE student_id = :student_id AND quarter = :quarter
    ");
    $stmt->execute([
        'student_id' => $student_id,
        'quarter' => $quarter
    ]);
    $grade_exists = $stmt->fetchColumn();
    if ($grade_exists > 0) {
        $_SESSION['error'] = "This quarter already graded."; 
        header("Location:  add_grade.php?student_id=$student_id");
        exit();
    }
    $grades = $_POST['grades'] ?? [];
    if (empty($grades)) {
        die("Error: No grades provided. Please provide grades for the subjects.");
    }

    foreach ($grades as $subject_id => $grade) {
        $written_test = $grade['written_test'] ?? 0;
        $performance_task = $grade['performance_task'] ?? 0;
        $exm = $grade['exm'] ?? 0;

        if (!is_numeric($written_test) || !is_numeric($performance_task) || !is_numeric($exm)) {
            die("Error: Invalid grade data. All grade values must be numeric.");
        }
        $s_grade = $written_test + $performance_task + $exm;

        $stmt = $pdo->prepare("
            INSERT INTO grades (student_id, subject_id, quarter, written_test, performance_task, exm, s_grade) 
            VALUES (:student_id, :subject_id, :quarter, :written_test, :performance_task, :exm, :s_grade)
            ON DUPLICATE KEY UPDATE
                written_test = VALUES(written_test),
                performance_task = VALUES(performance_task),
                exm = VALUES(exm),
                s_grade = VALUES(s_grade)
        ");

        $stmt->execute([
            'student_id' => $student_id,
            'subject_id' => $subject_id,
            'quarter' => $quarter,
            'written_test' => $written_test,
            'performance_task' => $performance_task,
            'exm' => $exm,
            's_grade' => $s_grade
        ]);

        $stmt = $pdo->prepare("
            SELECT id FROM student_card WHERE student_id = :student_id AND subject_id = :subject_id
        ");
        $stmt->execute([
            'student_id' => $student_id,
            'subject_id' => $subject_id
        ]);
        $student_card = $stmt->fetch();
        $column = '';
        switch ($quarter) {
            case 1:
                $column = '1st_quarter';
                break;
            case 2:
                $column = '2nd_quarter';
                break;
            case 3:
                $column = '3rd_quarter';
                break;
            case 4:
                $column = '4th_quarter';
                break;
            default:
                die("Error: Invalid quarter. Please provide a valid quarter (1, 2, 3, or 4).");
        }

        if ($student_card) {
            $stmt = $pdo->prepare("
                UPDATE student_card 
                SET $column = :s_grade 
                WHERE id = :student_card_id
            ");
            $stmt->execute([
                's_grade' => $s_grade,
                'student_card_id' => $student_card['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO student_card (student_id, subject_id, $column) 
                VALUES (:student_id, :subject_id, :s_grade)
            ");
            $stmt->execute([
                'student_id' => $student_id,
                'subject_id' => $subject_id,
                's_grade' => $s_grade
            ]);
        }
    }
    $_SESSION['success'] = "Grade submitted successfully!"; 
    header("Location: view_grades.php?student_id=$student_id");
    exit();
}
?>

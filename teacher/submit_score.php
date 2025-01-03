<?php
require_once '../utils/db_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (
        empty($_POST['student_id']) ||
        empty($_POST['quarter']) ||
        empty($_POST['subject_id']) ||
        empty($_POST['ww_1']) || empty($_POST['ww_2']) ||
        empty($_POST['ww_3']) || empty($_POST['ww_4']) ||
        empty($_POST['pt_1']) || empty($_POST['pt_2']) ||
        empty($_POST['pt_3']) || empty($_POST['pt_4']) ||
        empty($_POST['exam_1'])
    ) {
        session_start();
        $_SESSION['error'] = "All fields are required. Please fill out the form completely.";
        header("Location: add_score.php");
        exit;
    }
    // Get the input data from the form
    $student_id = $_POST['student_id'];
    $quarter = $_POST['quarter'];
    $subject_id = $_POST['subject_id'];
    
    // Written Works (WW) scores
    $ww_1 = $_POST['ww_1'];
    $ww_2 = $_POST['ww_2'];
    $ww_3 = $_POST['ww_3'];
    $ww_4 = $_POST['ww_4'];
    $ww_total = (($ww_1 + $ww_2 + $ww_3 + $ww_4) / 100) * 100 * 0.30;

    // Performance Tasks (PT) scores
    $pt_1 = $_POST['pt_1'];
    $pt_2 = $_POST['pt_2'];
    $pt_3 = $_POST['pt_3'];
    $pt_4 = $_POST['pt_4'];
    $pt_total = (($pt_1 + $pt_2 + $pt_3 + $pt_4) / 200) * 100 * 0.50;

    // Exam score
    $exam_1 = $_POST['exam_1'];
    $exam_total = ($exam_1 / 50) * 100 * 0.20;

    // Final Grade Calculation
    $total_grade = $ww_total + $pt_total + $exam_total;

    // Determine Pass/Fail Status
    $status = ($total_grade >= 75) ? 'passed' : 'failed';

    session_start();
    $teacher_id = $_SESSION['teacher_id'];

    // Prepare SQL query to insert the data into the class_record table
    $query = "INSERT INTO class_record (
        student_id, subject_id, quarter,
        ww_1, ww_2, ww_3, ww_4, ww_total,
        pt_1, pt_2, pt_3, pt_4, pt_total,
        exam_1, exam_total, teacher_id, created_at, updated_at
    ) VALUES (
        :student_id, :subject_id, :quarter, 
        :ww_1, :ww_2, :ww_3, :ww_4, :ww_total,
        :pt_1, :pt_2, :pt_3, :pt_4, :pt_total,
        :exam_1, :exam_total, :teacher_id, NOW(), NOW()
    )";

    // Prepare the statement using PDO
    $stmt = $pdo->prepare($query);

    // Bind the parameters
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
    $stmt->bindParam(':quarter', $quarter, PDO::PARAM_INT);
    $stmt->bindParam(':ww_1', $ww_1, PDO::PARAM_INT);
    $stmt->bindParam(':ww_2', $ww_2, PDO::PARAM_INT);
    $stmt->bindParam(':ww_3', $ww_3, PDO::PARAM_INT);
    $stmt->bindParam(':ww_4', $ww_4, PDO::PARAM_INT);
    $stmt->bindParam(':ww_total', $ww_total, PDO::PARAM_STR);
    $stmt->bindParam(':pt_1', $pt_1, PDO::PARAM_INT);
    $stmt->bindParam(':pt_2', $pt_2, PDO::PARAM_INT);
    $stmt->bindParam(':pt_3', $pt_3, PDO::PARAM_INT);
    $stmt->bindParam(':pt_4', $pt_4, PDO::PARAM_INT);
    $stmt->bindParam(':pt_total', $pt_total, PDO::PARAM_STR);
    $stmt->bindParam(':exam_1', $exam_1, PDO::PARAM_INT);
    $stmt->bindParam(':exam_total', $exam_total, PDO::PARAM_STR);
    $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // Calculate the total grade for the quarter
        $total_grade = $ww_total + $pt_total + $exam_total;

        // Determine the quarter field name based on the quarter number
        $quarter_field = '';
        switch ($quarter) {
            case 1:
                $quarter_field = '1st_quarter';
                break;
            case 2:
                $quarter_field = '2nd_quarter';
                break;
            case 3:
                $quarter_field = '3rd_quarter';
                break;
            case 4:
                $quarter_field = '4th_quarter';
                break;
        }

        // Check if the student and subject already have a record for the specified quarter
        $check_query = "SELECT * FROM student_card WHERE student_id = :student_id AND subject_id = :subject_id";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $check_stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            // Update the existing record for the specified quarter
            $update_query = "UPDATE student_card SET $quarter_field = :total_grade, status = :status, updated_at = NOW() 
            WHERE student_id = :student_id AND subject_id = :subject_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':total_grade', $total_grade, PDO::PARAM_STR);
            $update_stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $update_stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $update_stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
            if ($update_stmt->execute()) {
                $_SESSION['info'] = "Scores updated successfully.";
                header("Location: add_score.php");
                exit;
            } else {
                $_SESSION['error'] = "Error updating student card: " . $update_stmt->errorInfo()[2];
                header("Location: add_score.php");
                exit;
            }
        } else {
            // Insert the grade into the student_card table if not exists
            $card_query = "INSERT INTO student_card (
                student_id, subject_id, $quarter_field, status, created_at
            ) VALUES (:student_id, :subject_id, :total_grade, :status, NOW())";
            // Prepare the statement for student_card insertion
            $card_stmt = $pdo->prepare($card_query);
            $card_stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $card_stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
            $card_stmt->bindParam(':total_grade', $total_grade, PDO::PARAM_STR);
            $card_stmt->bindParam(':status', $status, PDO::PARAM_STR);
        
            // Execute the student_card query
            if ($card_stmt->execute()) {
                $_SESSION['success'] = "Scores submitted successfully.";
                header("Location: add_score.php");
                exit;
            } else {
                $_SESSION['error'] = "Error inserting into student card: " . $card_stmt->errorInfo()[2];
                header("Location: add_score.php");
                exit;
            }
        }
    } else {
        $_SESSION['error'] = "Error submitting the scores: " . $stmt->errorInfo()[2];
        header("Location: add_score.php");
        exit;
    }

    $pdo = null;
}
?>

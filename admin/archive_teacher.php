<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        
        $input = trim($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    function archiveTeacherRecord($teacher_id) {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // Check if the teacher exists
            $check_query = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
            $check_query->execute([$teacher_id]);
            $teacher = $check_query->fetch(PDO::FETCH_ASSOC);

            if (!$teacher) {
                throw new Exception("Teacher record not found");
            }

            // Prepare the insert statement for the teacher_archive table
            $insert_query = $pdo->prepare("INSERT INTO teacher_archive (teacher_id_num, full_name, birth_date, sex, religion, street, barangay, municipality, province, contact_number, grade, section, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $insert_query->execute([
                $teacher['teacher_id_num'],
                $teacher['full_name'],
                $teacher['birth_date'],
                $teacher['sex'],
                $teacher['religion'],
                $teacher['street'],
                $teacher['barangay'],
                $teacher['municipality'],
                $teacher['province'],
                $teacher['contact_number'],
                $teacher['grade'],
                $teacher['section'],
                $teacher['id'] // Store the original teacher ID
            ]);

            if (!$result) {
                throw new Exception("Failed to archive teacher record");
            }

            // Delete the teacher from the teachers table
            $delete_query = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $delete_result = $delete_query->execute([$teacher_id]);

            if (!$delete_result) {
                throw new Exception("Failed to delete teacher record");
            }

            $pdo->commit();

            return [
                'status' => 'success',
                'message' => 'Teacher record archived successfully.',
                'teacher_id' => $teacher_id
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("Teacher archiving error: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    try {
        $_POST = sanitizeInput($_POST); 

        // Reject requests other than POST requests for archiving
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Only POST requests are allowed");
        }

        // Check the teacher ID
        if (!isset($_POST['teacher_id']) || !is_numeric($_POST['teacher_id'])) {
            throw new Exception("Invalid or missing teacher ID");
        }

        // Archive the teacher record
        $result = archiveTeacherRecord(intval($_POST['teacher_id']));
        
        echo json_encode($result);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
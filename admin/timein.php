<?php
require_once '../utils/db_connection.php';
header('Content-Type: application/json');

// check existing time-in of teacher record
function checkExistingTimeIn($teacher_id) {
    global $pdo;
    
    $query = $pdo->prepare("
        SELECT COUNT(*) 
        FROM teacher_logs 
        WHERE teacher_id = :teacher_id 
        AND DATE(time_in) = CURDATE()
    ");
    
    $query->execute([':teacher_id' => $teacher_id]);
    return $query->fetchColumn() > 0;
}

function createTeacherLog($teacher_id, $time_in) {
    global $pdo;
    
    try {
        $teacherQuery = $pdo->prepare("
            SELECT teacher_id_num, full_name 
            FROM teachers 
            WHERE id = :teacher_id
        ");

        $teacherQuery->execute([':teacher_id' => $teacher_id]);
        $teacher = $teacherQuery->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            throw new Exception("Teacher not found");
        }

        if (checkExistingTimeIn($teacher_id)) {
            throw new Exception("Teacher already has a time-in record for today");
        }

        $query = $pdo->prepare("
            INSERT INTO teacher_logs (
                teacher_id, 
                teacher_name,
                time_in
            ) VALUES (
                :teacher_id,
                :teacher_name, 
                :time_in
            )
        ");

        $result = $query->execute([
            ':teacher_id' => $teacher_id,
            ':teacher_name' => $teacher['full_name'],
            ':time_in' => $time_in
        ]);

        if (!$result) {
            throw new Exception("Failed to create teacher log");
        }

        return [
            'teacher_name' => $teacher['full_name'],
            'teacher_id_num' => $teacher['teacher_id_num']
        ];
    } catch (Exception $e) {
        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['teacher_id']) || !isset($_POST['time_in'])) {
            throw new Exception("Missing required fields");
        }

        $teacher_id = $_POST['teacher_id'];
        $time_in = date('Y-m-d H:i:s', strtotime($_POST['time_in']));

        $result = createTeacherLog($teacher_id, $time_in);

        echo json_encode([
            'status' => 'success',
            'message' => 'Time-in recorded successfully',
            'data' => $result
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
<?php
session_start();
require_once("../utils/db_connection.php");
require_once('../utils/access_control.php');

checkAccess(['teacher']);

function getAttendanceStatsByGrade() {
    global $pdo;
    
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception("Teacher ID not found in session.");
    }

    $teacher_id = $_SESSION['teacher_id'];
    
    $query = "
        SELECT 
            s.grade,
            a.attendance,
            COUNT(*) as count
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        JOIN teacher_student_assignments tsa ON s.id = tsa.student_id
        WHERE tsa.teacher_id = :teacher_id
        AND s.grade IN ('Grade 7', 'Grade 8', 'Grade 9', 'Grade 10')
        GROUP BY s.grade, a.attendance
        ORDER BY s.grade, a.attendance
    ";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute(['teacher_id' => $teacher_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = [
            'Grade 7' => ['present' => 0, 'absent' => 0, 'late' => 0],
            'Grade 8' => ['present' => 0, 'absent' => 0, 'late' => 0],
            'Grade 9' => ['present' => 0, 'absent' => 0, 'late' => 0],
            'Grade 10' => ['present' => 0, 'absent' => 0, 'late' => 0]
        ];
        
        foreach ($results as $row) {
            $grade = $row['grade'];
            $attendance = strtolower($row['attendance']);
            if (isset($stats[$grade])) {
                $stats[$grade][$attendance] = (int)$row['count'];
            }
        }

        return [
            'present' => array_values(array_column($stats, 'present')),
            'absent' => array_values(array_column($stats, 'absent')),
            'late' => array_values(array_column($stats, 'late'))
        ];
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

try {
    $stats = getAttendanceStatsByGrade();
    header('Content-Type: application/json');
    echo json_encode($stats);
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['error' => $e->getMessage()]);
}
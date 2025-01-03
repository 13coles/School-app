<?php
session_start();
require_once("../utils/db_connection.php");

function getAttendanceStatsByGrade() {
    global $pdo;
    
    $query = "
        SELECT 
            s.grade,
            a.attendance,
            COUNT(*) as count
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE s.grade IN ('Grade 7', 'Grade 8', 'Grade 9', 'Grade 10')
        GROUP BY s.grade, a.attendance
        ORDER BY s.grade, a.attendance
    ";
    
    try {
        $stmt = $pdo->query($query);
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
        return ['error' => $e->getMessage()];
    }
}

$stats = getAttendanceStatsByGrade();
header('Content-Type: application/json');
echo json_encode($stats);
?>
<?php
session_start();
require_once '../utils/db_connection.php';
require_once '../utils/access_control.php';

header('Content-Type: application/json');

try {
    checkAccess(['teacher']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get date from request
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Calculate first and last day of month
        $monthYear = date('Y-m', strtotime($date));
        $firstDay = date('Y-m-01', strtotime($monthYear));
        $lastDay = date('Y-m-t', strtotime($monthYear));

        $query = "
            SELECT 
                s.id AS student_id, 
                s.full_name, 
                s.sex, 
                s.grade, 
                s.section,
                a.attendance_date,
                a.attendance,
                t.id AS teacher_id,
                t.full_name AS teacher_name
            FROM 
                students s
            LEFT JOIN 
                attendance a ON s.id = a.student_id 
                AND a.attendance_date BETWEEN :start_date AND :end_date
            LEFT JOIN 
                teachers t ON a.teacher_id = t.id
            WHERE t.id = :teacher_id
        ";

        $params = [
            ':start_date' => $firstDay,
            ':end_date' => $lastDay,
            ':teacher_id' => $_SESSION['teacher_id']
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $attendanceData = [];
        foreach ($records as $record) {
            $studentId = $record['student_id'];
            
            if (!isset($attendanceData[$studentId])) {
                $attendanceData[$studentId] = [
                    'student_id' => $studentId,
                    'full_name' => $record['full_name'],
                    'sex' => $record['sex'],
                    'grade' => $record['grade'],
                    'section' => $record['section'],
                    'attendance_dates' => []
                ];
            }
            
            if ($record['attendance_date'] && $record['attendance']) {
                $day = intval(date('j', strtotime($record['attendance_date'])));
                $attendanceData[$studentId]['attendance_dates'][$day] = strtoupper($record['attendance']);
            }
        }

        echo json_encode([
            'status' => 'success',
            'school_name' => 'Sewahon National High School',
            'school_year' => '2023-2024',
            'attendance' => array_values($attendanceData),
            'message' => count($attendanceData) > 0 ? 'Records found' : 'No records found'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
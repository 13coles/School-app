<?php
session_start();
require_once '../utils/db_connection.php';
require_once '../utils/access_control.php';

checkAccess(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $monthYear = $_GET['date'] ?? date('Y-m-d');
    $teacherId = $_GET['teacher_id'] ?? null;
    
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
        WHERE 1=1
    ";

    $params = [':start_date' => $firstDay, ':end_date' => $lastDay];

    if ($teacherId) {
        $query .= " AND (a.teacher_id = :teacher_id OR a.teacher_id IS NULL)";
        $params[':teacher_id'] = $teacherId;
    }

    $query .= " ORDER BY s.sex DESC, s.full_name";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restructure data for frontend
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
        'attendance' => array_values($attendanceData),
        'message' => count($attendanceData) > 0 ? 'Records found for printing' : 'No records found'
    ]);
    exit;
}
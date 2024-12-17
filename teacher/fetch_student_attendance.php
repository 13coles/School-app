<?php
session_start();
require_once '../utils/db_connection.php';
require_once '../utils/access_control.php';

checkAccess(['teacher']);

header('Content-Type: application/json');

try {
    // Verify teacher is logged in
    if (!isset($_SESSION['id'])) {
        throw new Exception("Unauthorized access");
    }

    $teacherId = $_SESSION['teacher_id'];
    $date = $_GET['date'] ?? date('Y-m-d');

    // Comprehensive query to fetch attendance with student details
    $query = "
        SELECT 
            a.id AS attendance_record_id,
            a.student_id, 
            a.attendance_date,
            a.attendance,
            a.teacher_id,
            s.lrn, 
            s.full_name, 
            s.sex, 
            s.grade, 
            s.section
        FROM 
            teacher_student_assignments tsa
        JOIN 
            students s ON tsa.student_id = s.id
        LEFT JOIN 
            attendance a ON a.student_id = s.id AND a.attendance_date = :attendance_date
        WHERE 
            tsa.teacher_id = :teacher_id
        ORDER BY 
            s.full_name
    ";

    // Prepare and execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':teacher_id' => $teacherId,
        ':attendance_date' => $date
    ]);
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return response
    echo json_encode([
        'status' => 'success',
        'attendance' => $attendanceRecords,
        'message' => $attendanceRecords ? 'Records found' : 'No records found'
    ]);

} catch (Exception $e) {
    // Error handling
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
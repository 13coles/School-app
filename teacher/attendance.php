<?php
session_start();
require_once '../utils/db_connection.php';
require_once '../utils/access_control.php';
checkAccess(['teacher']);

header('Content-Type: application/json');

try {
    error_log("Session Data: " . json_encode($_SESSION));
    error_log("POST Data: " . json_encode($_POST));

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
        throw new Exception("Unauthorized access: Not a teacher");
    }

    // Validate teacher_id from session
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception("Teacher ID not found in session");
    }

    $teacherId = $_SESSION['teacher_id'];
    $studentId = $_POST['student_id'] ?? null;
    $attendanceStatus = $_POST['attendance'] ?? null;
    $attendanceDate = date('Y-m-d'); 

    if (empty($studentId)) {
        throw new Exception("Student ID is required");
    }

    if (empty($attendanceStatus) || !in_array($attendanceStatus, ['present', 'absent'])) {
        throw new Exception("Invalid attendance status");
    }

    // Verify student exists
    $studentCheck = $pdo->prepare("SELECT id FROM students WHERE id = ?");
    $studentCheck->execute([$studentId]);
    if (!$studentCheck->fetch()) {
        throw new Exception("Student not found");
    }

    $pdo->beginTransaction();

    // 1. Insert/Update Attendance Record
    $attendanceQuery = "
        INSERT INTO attendance (
            student_id, 
            attendance_date, 
            attendance,
            teacher_id
        ) VALUES (
            :student_id, 
            :attendance_date, 
            :attendance,
            :teacher_id
        ) ON DUPLICATE KEY UPDATE 
            attendance = :attendance,
            teacher_id = :teacher_id
    ";

    $attendanceStmt = $pdo->prepare($attendanceQuery);
    $attendanceResult = $attendanceStmt->execute([
        ':student_id' => $studentId,
        ':attendance_date' => $attendanceDate,
        ':attendance' => $attendanceStatus,
        ':teacher_id' => $teacherId
    ]);

    if (!$attendanceResult) {
        throw new Exception("Failed to record attendance");
    }

    $pdo->commit();

    // Prepare response
    $response = [
        'status' => 'success',
        'message' => 'Attendance recorded successfully',
        'details' => [
            'student_id' => $studentId,
            'attendance' => $attendanceStatus,
            'date' => $attendanceDate,
            'teacher_id' => $teacherId
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // Rollback transaction in case of error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Attendance Error: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());

    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
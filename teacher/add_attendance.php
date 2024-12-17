<?php
session_start();
require_once '../utils/db_connection.php';
require_once '../utils/access_control.php';
checkAccess(['teacher']);

header('Content-Type: application/json');

try {
    error_log("Attendance Submission - Session Data: " . json_encode($_SESSION));
    error_log("Attendance Submission - POST Data: " . json_encode($_POST));

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
        throw new UnauthorizedAccessException("Unauthorized access: Not a teacher");
    }

    if (!isset($_SESSION['teacher_id'])) {
        throw new SessionException("Teacher ID not found in session");
    }

    $teacherId = $_SESSION['teacher_id'];
    $studentId = $_POST['student_id'] ?? null;
    $attendanceStatus = strtolower($_POST['attendance'] ?? null);
    $attendanceDate = date('Y-m-d'); 

    if (empty($studentId)) {
        throw new ValidationException("Student ID is required");
    }

    if (empty($attendanceStatus) || !in_array($attendanceStatus, ['present', 'absent', 'late'])) {
        throw new ValidationException("Invalid attendance status");
    }

    $studentAssignmentCheck = $pdo->prepare("
        SELECT 1 FROM teacher_student_assignments 
        WHERE teacher_id = :teacher_id AND student_id = :student_id
    ");
    $studentAssignmentCheck->execute([
        ':teacher_id' => $teacherId,
        ':student_id' => $studentId
    ]);

    if (!$studentAssignmentCheck->fetch()) {
        throw new AccessDeniedException("Student not assigned to this teacher");
    }

    // Check if attendance record exists for today
    $checkAttendanceQuery = $pdo->prepare("
        SELECT id FROM attendance 
        WHERE student_id = :student_id 
        AND attendance_date = :attendance_date
    ");
    $checkAttendanceQuery->execute([
        ':student_id' => $studentId,
        ':attendance_date' => $attendanceDate
    ]);
    $existingAttendance = $checkAttendanceQuery->fetch(PDO::FETCH_ASSOC);

    $pdo->beginTransaction();

    // update attendance for the current date if student attendance already exists
    if ($existingAttendance) {
        $updateQuery = "
            UPDATE attendance 
            SET 
                attendance = :attendance,
                teacher_id = :teacher_id
            WHERE 
                student_id = :student_id 
                AND attendance_date = :attendance_date
        ";

        $updateStmt = $pdo->prepare($updateQuery);
        $updateResult = $updateStmt->execute([
            ':student_id' => $studentId,
            ':attendance_date' => $attendanceDate,
            ':attendance' => $attendanceStatus,
            ':teacher_id' => $teacherId
        ]);

        if (!$updateResult) {
            throw new DatabaseException("Failed to update attendance");
        }
    } 
    // If no existing attendance, insert new record
    else {
        $insertQuery = "
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
            )
        ";

        $insertStmt = $pdo->prepare($insertQuery);
        $insertResult = $insertStmt->execute([
            ':student_id' => $studentId,
            ':attendance_date' => $attendanceDate,
            ':attendance' => $attendanceStatus,
            ':teacher_id' => $teacherId
        ]);

        if (!$insertResult) {
            throw new DatabaseException("Failed to record attendance");
        }
    }

    $pdo->commit();

    $response = [
        'status' => 'success',
        'message' => $existingAttendance 
            ? 'Attendance updated successfully' 
            : 'Attendance recorded successfully',
        'details' => [
            'student_id' => $studentId,
            'attendance' => $attendanceStatus,
            'date' => $attendanceDate,
            'teacher_id' => $teacherId
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $statusCode = 400;
    $errorType = 'ValidationError';
    $errorMessage = $e->getMessage();

    if ($e instanceof UnauthorizedAccessException) {
        $statusCode = 403;
        $errorType = 'AuthorizationError';
    } elseif ($e instanceof SessionException) {
        $statusCode = 401;
        $errorType = 'SessionError';
    } elseif ($e instanceof AccessDeniedException) {
        $statusCode = 403;
        $errorType = 'AccessDeniedError';
    } elseif ($e instanceof DatabaseException) {
        $statusCode = 500;
        $errorType = 'DatabaseError';
    }

    error_log("Attendance Error [{$errorType}]: " . $errorMessage);

    http_response_code($statusCode);
    echo json_encode([
        'status' => 'error',
        'type' => $errorType,
        'message' => $errorMessage
    ]);
    exit;
}

class UnauthorizedAccessException extends Exception {}
class SessionException extends Exception {}
class ValidationException extends Exception {}
class AccessDeniedException extends Exception {}
class DatabaseException extends Exception {}
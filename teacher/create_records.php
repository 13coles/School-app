<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    # same thing lng sa admin side na logic ah
    function createStudentRecord($data) {
        global $pdo;
    
        try {
            $pdo->beginTransaction();
    
            error_log("Received student data: " . print_r($data, true));
    
            $requiredFields = ['lrn', 'full_name', 'birth_date', 'sex', 'barangay', 'municipality', 'province', 'grade', 'section', 'learning_modality'];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    error_log("Missing or empty field: $field");
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }
    
            $check_student_query = $pdo->prepare("SELECT COUNT(*) FROM students WHERE lrn = ?");
            $check_student_query->execute([$data['lrn']]);
            if ($check_student_query->fetchColumn() > 0) {
                throw new Exception("Student with this LRN already exists");
            }
    
            $check_user_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $check_user_query->execute([$data['lrn']]);
            if ($check_user_query->fetchColumn() > 0) {
                throw new Exception("User account with this LRN already exists");
            }
    
            $student_query = $pdo->prepare("
                INSERT INTO students (
                    lrn, full_name, 
                    birth_date, sex, religion, 
                    street, barangay, municipality, province, contact_number,
                    father_name, mother_name, guardian_name, relationship, guardian_contact,
                    grade, section, learning_modality, remarks
                ) VALUES (
                    :lrn, :full_name, 
                    :birth_date, :sex, :religion, 
                    :street, :barangay, :municipality, :province, :contact_number,
                    :father_name, :mother_name, :guardian_name, :relationship, :guardian_contact,
                    :grade, :section, :learning_modality, :remarks
                )
            ");
    
            $student_result = $student_query->execute([
                ':lrn' => $data['lrn'],
                ':full_name' => $data['full_name'],
                ':birth_date' => $data['birth_date'],
                ':sex' => $data['sex'],
                ':religion' => $data['religion'] ?? null,
                ':street' => $data['street'] ?? null,
                ':barangay' => $data['barangay'],
                ':municipality' => $data['municipality'],
                ':province' => $data['province'],
                ':contact_number' => $data['contact_number'] ?? null,
                ':father_name' => $data['father_name'] ?? null,
                ':mother_name' => $data['mother_name'] ?? null,
                ':guardian_name' => $data['guardian_name'] ?? null,
                ':relationship' => $data['relationship'] ?? null,
                ':guardian_contact' => $data['guardian_contact'] ?? null,
                ':grade' => $data['grade'],
                ':section' => $data['section'],
                ':learning_modality' => $data['learning_modality'],
                ':remarks' => $data['remarks'] ?? null
            ]);
    
            if (!$student_result) {
                $errorInfo = $student_query->errorInfo();
                throw new Exception("Student record insertion failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }
    
            $defaultPassword = 'student';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);
    
            $user_query = $pdo->prepare("
                INSERT INTO users (
                    username, password, user_role, 
                    full_name, email, contact_number
                ) VALUES (
                    :username, :password, 'student', 
                    :full_name, :email, :contact_number
                )
            ");
    
            $user_result = $user_query->execute([
                ':username' => $data['lrn'],
                ':password' => $hashedPassword,
                ':full_name' => $data['full_name'],
                ':email' => null,
                ':contact_number' => $data['contact_number'] ?? null
            ]);
    
            if (!$user_result) {
                $errorInfo = $user_query->errorInfo();
                throw new Exception("User account creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }
    
            $pdo->commit();
    
            echo json_encode([
                'status' => 'success',
                'message' => 'Student record and user account created successfully',
                'lrn' => $data['lrn'],
                'default_password' => $defaultPassword
            ]);
            exit;
    
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
    
            error_log("Student record creation error: " . $e->getMessage());
            
            header('HTTP/1.1 400 Bad Request');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Raw POST data: " . print_r($_POST, true));
        
        createStudentRecord($_POST);
    }
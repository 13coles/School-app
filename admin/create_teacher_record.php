<?php
require_once '../utils/db_connection.php';

header('Content-Type: application/json');

// Function for randomizing teacher username ID
function generateTeacherID($prefix) {
    global $pdo;

    try {
        $maxAttempts = 10; // Limiting the random number generation to 10 attempts
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Generate random numbers
            $randomNum = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT); // 5 digits, starting from 0
            $teacherID = $prefix . $randomNum; 

            // Prepare the query to check from the users table and check if the generated username already exists
            $query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $query->execute([$teacherID]); 
            
            // Return the generated username if it is unique
            if ($query->fetchColumn() == 0) {
                return $teacherID;
            }
        }
        // Throw error if the max attempt is reached
        throw new Exception("Could not generate unique teacher ID");
    } catch (Exception $e) {
        error_log("Teacher ID generation error: " . $e->getMessage());
        return null;
    }
}

// Function to create teacher record
function createTeacherRecord($data) {
    global $pdo;

    try {
        // Start a database transaction
        $pdo->beginTransaction();

        // Generate a unique teacher ID with the prefix
        $data['teacher_id_num'] = generateTeacherID('tch-');
        if (!$data['teacher_id_num']) {
            throw new Exception("Failed to generate unique teacher ID");
        }

        // Validate required fields
        $requiredFields = [
            'full_name', 'birth_date', 
            'sex', 'barangay', 'municipality', 'province', 
            'contact_number'
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Field '$field' is required and cannot be empty");
            }
        }

        // Check for existing teacher with the same ID
        $check_teacher_query = $pdo->prepare("SELECT COUNT(*) FROM teachers WHERE teacher_id_num = ?");
        $check_teacher_query->execute([$data['teacher_id_num']]);
        if ($check_teacher_query->fetchColumn() > 0) {
            throw new Exception("Teacher with this ID already exists");
        }

        // Prepare and execute teacher insertion query
        $teacher_query = $pdo->prepare("
            INSERT INTO teachers (
                teacher_id_num, full_name, 
                birth_date, sex, religion, 
                street, barangay, municipality, province, 
                contact_number, grade, section
            ) VALUES (
                :teacher_id_num, :full_name, 
                :birth_date, :sex, :religion, 
                :street, :barangay, :municipality, :province, 
                :contact_number, :grade, :section
            )
        ");

        $teacher_result = $teacher_query->execute([
            ':teacher_id_num' => $data['teacher_id_num'],
            ':full_name' => $data['full_name'],
            ':birth_date' => $data['birth_date'],
            ':sex' => $data['sex'],
            ':religion' => $data['religion'] ?? null,
            ':street' => $data['street'] ?? null,
            ':barangay' => $data['barangay'],
            ':municipality' => $data['municipality'],
            ':province' => $data['province'],
            ':contact_number' => $data['contact_number'],
            ':grade' => $data['grade'] ?? null, 
            ':section' => $data['section'] ?? null 
        ]);

        // Check if teacher insertion was successful
        if (!$teacher_result) {
            $errorInfo = $teacher_query->errorInfo();
            throw new Exception("Teacher record creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
        }

        $teacherId = $pdo->lastInsertId();

        // Create user account for teacher
        $defaultPassword = 'teacher'; 
        $hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);
    
        $user_query = $pdo->prepare("
            INSERT INTO users (
                username, password, user_role, 
                full_name, email, contact_number, student_id, teacher_id
            ) VALUES (
                :username, :password, :user_role, 
                :full_name, :email, :contact_number,
                :student_id, :teacher_id
            )
        ");
        
        # Pass the values and create the account referencing to the created teacher record
        $result = $user_query->execute([
            ':username' => $data['teacher_id_num'], 
            ':password' => $hashedPassword,
            ':user_role' => 'teacher', 
            ':full_name' => $data['full_name'],
            ':email' => $data['email'] ?? null,
            ':contact_number' => $data['contact_number'] ?? null,
            ':student_id' => null,
            ':teacher_id' => $teacherId
        ]);

        if (!$result) {
            $errorInfo = $user_query->errorInfo();
            throw new Exception("User  creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
        }

        // Commit the transaction
        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Teacher created successfully',
            'teacher_id' => $data['teacher_id_num']
        ]);
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log("Teacher creation error: " . $e->getMessage());
        
        header('HTTP/1.1 400 Bad Request');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Condition to handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Raw POST data: " . print_r($_POST, true));
    
    createTeacherRecord($_POST); // Call the function to create teacher record
}
exit;
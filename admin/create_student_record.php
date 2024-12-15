<?php
    require_once '../utils/db_connection.php';
    
    header('Content-Type: application/json'); 

    # function for creating student record
    function createStudentRecord($data) {
        global $pdo;
    
        try {
            # start a database transaction to ensure data integrity
            $pdo->beginTransaction();
    
            # defining important student information fields
            $requiredFields = ['lrn', 'full_name', 'birth_date', 'sex', 'barangay', 'municipality', 'province', 'grade', 'section', 'learning_modality'];
    
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    error_log("Missing or empty field: $field");
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }
    
            # check for existing student and user
            $check_student_query = $pdo->prepare("SELECT COUNT(*) FROM students WHERE lrn = ?");
            $check_student_query->execute([$data['lrn']]);
            if ($check_student_query->fetchColumn() > 0) {
                throw new Exception("Student with this LRN already exists");
            }
    
            # insert new student record
            $student_query = $pdo->prepare("
                INSERT INTO students (
                    lrn, full_name, birth_date, sex, religion, street, barangay, municipality, province, contact_number,
                    father_name, mother_name, guardian_name, relationship, guardian_contact,
                    grade, section, learning_modality, remarks
                ) VALUES (
                    :lrn, :full_name, :birth_date, :sex, :religion, :street, :barangay, :municipality, :province, :contact_number,
                    :father_name, :mother_name, :guardian_name, :relationship, :guardian_contact,
                    :grade, :section, :learning_modality, :remarks
                )
            ");
            
            $student_query->execute([
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
    
            $studentId = $pdo->lastInsertId();
    
            # Insert user account logic (as you already have it)
            $defaultPassword = 'student';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);
    
            $user_query = $pdo->prepare("
                INSERT INTO users (
                    username, password, user_role, full_name, email, contact_number, student_id
                ) VALUES (
                    :username, :password, 'student', :full_name, :email, :contact_number, :student_id
                )
            ");
    
            $user_query->execute([
                ':username' => $data['lrn'],
                ':password' => $hashedPassword,
                ':full_name' => $data['full_name'],
                ':email' => null,
                ':contact_number' => $data['contact_number'] ?? null,
                ':student_id' => $studentId
            ]);
    
            # Now associate the student with all subjects
            // Fetch all subject ids
            $subject_query = $pdo->query("SELECT id FROM subjects");
            $subjects = $subject_query->fetchAll(PDO::FETCH_ASSOC);
    
            // Insert student-subject associations into the pivot table
            $insert_subjects_query = $pdo->prepare("
                INSERT INTO student_subject (student_id, subject_id) VALUES (:student_id, :subject_id)
            ");
            
            foreach ($subjects as $subject) {
                $insert_subjects_query->execute([
                    ':student_id' => $studentId,
                    ':subject_id' => $subject['id']
                ]);
            }
    

            $pdo->commit();
    
            echo json_encode([
                'status' => 'success',
                'message' => 'Student record, user account, and subject associations created successfully',
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
    

    # Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        # for debugging purpose
        error_log("Raw POST data: " . print_r($_POST, true));
        
        createStudentRecord($_POST); # call the function to create the student record and default account if it is a POST request
    }
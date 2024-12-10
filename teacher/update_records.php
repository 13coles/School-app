<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        
        $input = trim($input);
        $input = strip_tags($input);
        
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    function fetchStudentDetails($student_id) {
        global $pdo;

        try {
            $query = $pdo->prepare("
                SELECT 
                    id, 
                    lrn, 
                    full_name,
                    birth_date, 
                    sex, 
                    religion, 
                    street, 
                    barangay, 
                    municipality, 
                    province, 
                    contact_number,
                    father_name, 
                    mother_name, 
                    guardian_name, 
                    relationship, 
                    guardian_contact,
                    grade, 
                    section, 
                    learning_modality, 
                    remarks
                FROM students 
                WHERE id = :student_id
            ");

            $query->execute(['student_id' => $student_id]);
            
            if ($query->errorCode() !== '00000') {
                $errorInfo = $query->errorInfo();
                throw new Exception("Database query error: " . $errorInfo[2]);
            }

            $student = $query->fetch(PDO::FETCH_ASSOC);

            error_log("Student Data Fetched: " . print_r($student, true));

            if (!$student) {
                throw new Exception("No student found with ID: $student_id");
            }

            $nameParts = explode(',', $student['full_name']);
            $lastNamePart = trim($nameParts[0] ?? '');
            $firstAndMiddleNames = isset($nameParts[1]) ? trim($nameParts[1]) : '';
            
            $nameWords = explode(' ', $firstAndMiddleNames);
            $firstName = $nameWords[0] ?? '';
            $middleName = implode(' ', array_slice($nameWords, 1)) ?? '';

            $student['last_name'] = $lastNamePart;
            $student['first_name'] = $firstName;
            $student['middle_name'] = $middleName;

            return $student;

        } catch (Exception $e) {
            error_log("Error fetching student details: " . $e->getMessage());
            throw $e;
        }
    }

    function updateStudentRecord($data) {
        global $pdo;

        try {
            error_log("Received student update data: " . print_r($data, true));

            $requiredFields = ['student_id', 'lrn', 'full_name', 'birth_date', 'sex', 'barangay', 'municipality', 'province', 'grade', 'section', 'learning_modality'];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    error_log("Missing or empty field: $field");
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }

            $check_query = $pdo->prepare("SELECT COUNT(*) FROM students WHERE id = ?");
            $check_query->execute([$data['student_id']]);
            if ($check_query->fetchColumn() == 0) {
                throw new Exception("Student record not found");
            }

            $lrn_check_query = $pdo->prepare("SELECT COUNT(*) FROM students WHERE lrn = ? AND id != ?");
            $lrn_check_query->execute([$data['lrn'], $data['student_id']]);
            if ($lrn_check_query->fetchColumn() > 0) {
                throw new Exception("LRN is already in use by another student");
            }

            $pdo->beginTransaction();

            $query = $pdo->prepare("
                UPDATE students SET 
                    lrn = :lrn, 
                    full_name = :full_name, 
                    birth_date = :birth_date, 
                    sex = :sex, 
                    religion = :religion, 
                    street = :street, 
                    barangay = :barangay, 
                    municipality = :municipality, 
                    province = :province, 
                    contact_number = :contact_number,
                    father_name = :father_name, 
                    mother_name = :mother_name, 
                    guardian_name = :guardian_name, 
                    relationship = :relationship, 
                    guardian_contact = :guardian_contact,
                    grade = :grade, 
                    section = :section, 
                    learning_modality = :learning_modality, 
                    remarks = :remarks,
                    updated_at = NOW()
                WHERE id = :student_id
            ");

            $result = $query->execute([
                ':student_id' => $data['student_id'],
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

            if (!$result) {
                $pdo->rollBack();
                $errorInfo = $query->errorInfo();
                throw new Exception("Database update failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }

            $pdo->commit();

            return [
                'status' => 'success',
                'message' => 'Student record updated successfully',
                'student_id' => $data['student_id']
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Student record update error: " . $e->getMessage());
            throw $e;
        }
    }

    try {
        $_GET = sanitizeInput($_GET);
        $_POST = sanitizeInput($_POST);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception("Invalid or missing student ID");
            }

            $student = fetchStudentDetails(intval($_GET['id']));
                    echo json_encode([
                        'status' => 'success',
                        'student' => $student,
                        'debug_id' => intval($_GET['id'])
                    ]);
        
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # update student record
            # ensure student_id is present
            if (!isset($_POST['student_id'])) {
                throw new Exception('Student ID is required for update');
            }
        
            # combine names if separate fields are provided
            if (isset($_POST['last_name']) && isset($_POST['first_name'])) {
                $fullName = $_POST['last_name'];
                $fullName .= $_POST['first_name'] ? ', ' . $_POST['first_name'] : '';
                $fullName .= $_POST['middle_name'] ? ' ' . $_POST['middle_name'] : '';
                $_POST['full_name'] = $fullName;
            }
        
            try {
                $result = updateStudentRecord($_POST);
        
                echo json_encode($result);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
    
        } else {
            throw new Exception("Unsupported HTTP method");
        }
            
    } catch (Exception $e) {
        error_log("Error in student_details.php: " . $e->getMessage());
        
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'debug_trace' => $e->getTraceAsString()
        ]);
    }
    exit;
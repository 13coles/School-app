<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        
        $input = trim($input);
        $input = strip_tags($input);
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    function fetchTeacherDetails($teacher_id) {
        global $pdo;

        try {
            $query = $pdo->prepare("
                SELECT 
                    id, 
                    teacher_id_num, 
                    full_name, 
                    birth_date, 
                    sex, 
                    religion, 
                    street, 
                    barangay, 
                    municipality, 
                    province, 
                    contact_number,
                    grade,
                    section
                FROM teachers 
                WHERE id = :teacher_id
            ");

            $query->execute(['teacher_id' => $teacher_id]);
            
            if ($query->errorCode() !== '00000') {
                $errorInfo = $query->errorInfo();
                throw new Exception("Database query error: " . $errorInfo[2]);
            }

            $teacher = $query->fetch(PDO::FETCH_ASSOC);

            if (!$teacher) {
                throw new Exception("No teacher found with ID: $teacher_id");
            }

            $nameParts = explode(',', $teacher['full_name']);
            $lastNamePart = trim($nameParts[0] ?? '');
            $firstAndMiddleNames = isset($nameParts[1]) ? trim($nameParts[1]) : '';
            
            $nameWords = explode(' ', $firstAndMiddleNames);
            $firstName = $nameWords[0] ?? '';
            $middleName = implode(' ', array_slice($nameWords, 1)) ?? '';

            $teacher['last_name'] = $lastNamePart;
            $teacher['first_name'] = $firstName;
            $teacher['middle_name'] = $middleName;

            return $teacher;

        } catch (Exception $e) {
            error_log("Error fetching teacher details: " . $e->getMessage());
            throw $e;
        }
    }

    function updateTeacherRecord($data) {
        global $pdo;

        try {
            $requiredFields = ['teacher_id', 'teacher_id_num', 'full_name', 'birth_date', 'sex', 'barangay', 'municipality', 'province', 'contact_number'];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }

            $check_query = $pdo->prepare("SELECT COUNT(*) FROM teachers WHERE id = ?");
            $check_query->execute([$data['teacher_id']]);
            if ($check_query->fetchColumn() == 0) {
                throw new Exception("Teacher record not found");
            }

            $pdo->beginTransaction();

            $query = $pdo->prepare("
                UPDATE teachers SET 
                    teacher_id_num = :teacher_id_num, 
                    full_name = :full_name, 
                    birth_date = :birth_date, 
                    sex = :sex, 
                    religion = :religion, 
                    street = :street, 
                    barangay = :barangay, 
                    municipality = :municipality, 
                    province = :province, 
                    contact_number = :contact_number,
                    grade = :grade,
                    section = :section
                WHERE id = :teacher_id
            ");

            $result = $query->execute([
                ':teacher_id' => $data['teacher_id'],
                ':teacher_id_num' => $data['teacher_id_num'],
                ':full_name' => $data['full_name'],
                ':birth_date' => $data['birth_date'],
                ':sex' => $data['sex'],
                ':religion' => $data['religion'] ?? null,
                ':street' => $data['street'] ?? null,
                ':barangay' => $data['barangay'],
                ':municipality' => $data['municipality'],
                ':province' => $data['province'],
                ':contact_number' => $data['contact_number'] ?? null,
                ':grade' => $data['grade'] ?? null,
                ':section' => $data['section'] ?? null
            ]);

            if (!$result) {
                $pdo->rollBack();
                $errorInfo = $query->errorInfo();
                throw new Exception("Database update failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }

            $pdo->commit();

            return [
                'status' => 'success',
                'message' => 'Teacher record updated successfully',
                'teacher_id' => $data['teacher_id']
            ];

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("Teacher record update error: " . $e->getMessage());
            throw $e;
        }
    }

    try {
        // Sanitizing GET and POST requests
        $_GET = sanitizeInput($_GET);
        $_POST = sanitizeInput($_POST);

        // If GET request, return the teacher details to edit
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception("Invalid or missing teacher ID");
            }

            // Call the fetch function
            $teacher = fetchTeacherDetails(intval($_GET['id']));
            echo json_encode([
                'status' => 'success',
                'teacher' => $teacher,
                'debug_id' => intval($_GET['id'])
            ]);

        // If POST request, update the teacher record
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ensure teacher_id is present
            if (!isset($_POST['teacher_id'])) {
                throw new Exception('Teacher ID is required for update');
            }

            // Combine names if separate fields are provided
            if (isset($_POST['last_name']) && isset($_POST['first_name'])) {
                $fullName = $_POST['last_name'];
                $fullName .= $_POST['first_name'] ? ', ' . $_POST['first_name'] : '';
                $fullName .= $_POST['middle_name'] ? ' ' . $_POST['middle_name'] : '';
                $_POST['full_name'] = $fullName;
            }

            try {
                // Call the update function 
                $result = updateTeacherRecord($_POST);
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
        error_log("Error in update_teachers.php: " . $e->getMessage());
        
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'debug_trace' => $e->getTraceAsString()
        ]);
    }
    exit;
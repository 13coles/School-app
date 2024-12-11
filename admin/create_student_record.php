<?php
    require_once '../utils/db_connection.php';
    
    header('Content-Type: application/json'); 

    # function for creating student record
    function createStudentRecord($data) {
        global $pdo;
    
        try {
            # start a database transaction to ensure data integrity
            $pdo->beginTransaction();
    
            # log incoming student data for debugging
            error_log("Received student data: " . print_r($data, true));
    
            # defining important student information fields to ensure the necessary fields are filled out
            $requiredFields = ['lrn', 'full_name', 'birth_date', 'sex', 'barangay', 'municipality', 'province', 'grade', 'section', 'learning_modality'];
            
            # backend side validation to check each required fields by looping through the required fields array 
            # and checks if the field exists in the incoming request specifically the required fields.
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    error_log("Missing or empty field: $field");
                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }
    
            # checking existing student record in the database based on the LRN of the incoming request 
            # and throw an exception error if the LRN already exists
            $check_student_query = $pdo->prepare("SELECT COUNT(*) FROM students WHERE lrn = ?");
            $check_student_query->execute([$data['lrn']]);
            if ($check_student_query->fetchColumn() > 0) {
                throw new Exception("Student with this LRN already exists");
            }
    
            # reject user account creation request if there is an existing account with the same LRN
            $check_user_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $check_user_query->execute([$data['lrn']]);
            if ($check_user_query->fetchColumn() > 0) {
                throw new Exception("User account with this LRN already exists");
            }
    
            # preparing the students table for inserting new student data
            # used paramters to pass the incoming request data to the query to prevent SQL injection
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
    
            # insert the new student data into the students table passing the request data into the parameters of each table column
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
    
            # display error message if the creation failed
            if (!$student_result) {
                $errorInfo = $student_query->errorInfo();
                throw new Exception("Student record creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }
    
            # get the last inserted id of the student data
            $studentId = $pdo->lastInsertId();
    
            # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
            #       This part will handle the creation of user account of the student automatically       # 
            # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

            # provide a default password for the account creation, in this case, it is student
            # changeable default password ah
            $defaultPassword = 'student';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT); # hash the new password for security purposes
    
            # prepare the users table before inserting the new user account data
            $user_query = $pdo->prepare("
                INSERT INTO users (
                    username, password, user_role, 
                    full_name, email, contact_number, 
                    student_id
                ) VALUES (
                    :username, :password, 'student', 
                    :full_name, :email, :contact_number,
                    :student_id
                )
            ");
    
            # create user account, the username is the student's lrn filled in the students table while the password is default.
            # contact number is also passed and capture the student id from the students table and pass it in the foreign key column student_id
            $user_result = $user_query->execute([
                ':username' => $data['lrn'],
                ':password' => $hashedPassword,
                ':full_name' => $data['full_name'],
                ':email' => null,
                ':contact_number' => $data['contact_number'] ?? null,
                ':student_id' => $studentId
            ]);
    
            # display error message if account creation failed
            if (!$user_result) {
                $errorInfo = $user_query->errorInfo();
                throw new Exception("User account creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }
    
            # commit database transaction
            $pdo->commit();
    
            echo json_encode([
                'status' => 'success',
                'message' => 'Student record and user account created successfully',
                'lrn' => $data['lrn'], 
                'default_password' => $defaultPassword
            ]);
            exit;
    
        } catch (Exception $e) {
            # rollback the transaction if it failed during the creation process to ensure no failed data is being inserted in the database
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
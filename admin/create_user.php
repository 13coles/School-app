<?php
    require_once '../utils/db_connection.php';

    header('Content-Type: application/json');

    # function for randomizing teacher username id
    function generateTeacherID() {
        global $pdo;

        try {
            $prefix = 'tch-';
            $maxAttempts = 10; # limiting the random number generation to 10 attempts
            
            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                # generate random numbers
                $randomNum = str_pad(mt_rand(0, 9999), 5, '0', STR_PAD_LEFT); # 5 digits, starting with zero
                $username = $prefix . $randomNum; 

                # prepare the query to check from the usrs table and check if the generated username already exists
                $query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                $query->execute([$username]); 
                
                # return the generated username if it is unique
                if ($query->fetchColumn() == 0) {
                    return $username;
                }
            }
            # throw error if the max attempt is reached
            throw new Exception("Could not generate unique teacher ID");
        } catch (Exception $e) {
            error_log("Teacher ID generation error: " . $e->getMessage());
            return null;
        }
    }

    # function to create student record
    function createStudentRecord($lrn, $full_name, $contact_number) {
        global $pdo;

        try {
            # prepare the query to create the student record automatically after creating an account for the usr
            $query = $pdo->prepare("
                INSERT INTO students (
                    lrn, 
                    full_name,
                    contact_number
                ) VALUES (
                    :lrn, 
                    :full_name,
                    :contact_number,
                )
            ");

            # pass the parameter values from the input such as the full name, lrn and contact number
            # this will be the first student record values of the students, the rest will be null and the student can edit those themselves later
            $result = $query->execute([
                ':lrn' => $lrn,
                ':full_name' => $full_name,
                ':contact_number' => $contact_number,
            ]);

            if (!$result) {
                $errorInfo = $query->errorInfo();
                throw new Exception("Student record creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }

            return true;

        } catch (Exception $e) {
            error_log("Student record creation error: " . $e->getMessage());
            throw $e;
        }
    }

    # function to create user account
    function createUser($data) {
        global $pdo;

        try {
            error_log("Received data: " . print_r($data, true));

            $requiredFields = ['username', 'password', 'user_role', 'full_name', 'email'];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') {
                    error_log("Missing or empty field: $field");

                    throw new Exception("Field '$field' is required and cannot be empty");
                }
            }

            # checking existing usrname
            $check_query = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $check_query->execute([$data['username']]);
            if ($check_query->fetchColumn() > 0) {
                throw new Exception("Username already exists");
            }

            # validating email to ensure it is in the proper format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            $pdo->beginTransaction();

            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            $studentId = null; 
            # this id will be used to identify the student record created and pass it to the foreign key column in the users table

            # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
            #      This part will handle the creation of student record automatically  during user account creation     # 
            # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

            # if user is a student, create the student record first (can't create the account first if the student record is empty)
            if ($data['user_role'] === 'student') {
                # prepare the query the default student record values
                # lrn is derived from the user account's username
                # full name is from the full name of the user account
                $studentQuery = $pdo->prepare("
                    INSERT INTO students (
                        lrn, 
                        full_name,
                        contact_number
                    ) VALUES (
                        :lrn, 
                        :full_name,
                        :contact_number
                    )
                ");

                # pass the values
                $studentQuery->execute([
                    ':lrn' => $data['username'],
                    ':full_name' => $data['full_name'],
                    ':contact_number' => $data['contact_number'] ?? null
                ]);

                # assign the last inserted student ID
                $studentId = $pdo->lastInsertId();
            }

            # proceed to querying the user account creation
            $query = $pdo->prepare("
                INSERT INTO users (
                    username, 
                    password, 
                    user_role, 
                    full_name, 
                    email, 
                    contact_number,
                    student_id 
                ) VALUES (
                    :username, 
                    :password, 
                    :user_role, 
                    :full_name, 
                    :email, 
                    :contact_number,
                    :student_id
                )
            ");
            
            # pass the values and create the account referencing to the created student record
            $result = $query->execute([
                ':username' => $data['username'],
                ':password' => $hashedPassword,
                ':user_role' => $data['user_role'],
                ':full_name' => $data['full_name'],
                ':email' => $data['email'],
                ':contact_number' => $data['contact_number'] ?? null,
                ':student_id' => $studentId
            ]);

            if (!$result) {
                $errorInfo = $query->errorInfo();
                throw new Exception("User creation failed: " . ($errorInfo[2] ?? 'Unknown error'));
            }

            $pdo->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'User created successfully',
                'username' => $data['username']
            ]);
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("User creation error: " . $e->getMessage());
            
            header('HTTP/1.1 400 Bad Request');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    # condition to handle incoming get request and post request respectively
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Raw POST data: " . print_r($_POST, true));
        
        createUser($_POST); # if post request, call the function to create user

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        # if get request, return the random username generation to fill the username of the teacher
        if (isset($_GET['action']) && $_GET['action'] === 'generate_teacher_id') {
            $teacherID = generateTeacherID();

            if ($teacherID) {
                echo json_encode([
                    'status' => 'success',
                    'username' => $teacherID
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to generate teacher ID'
                ]);
            }
            exit;
        }
    }
    exit;
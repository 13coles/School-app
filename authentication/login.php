<?php
    session_start();
    require_once ('../utils/db_connection.php');

    function sanitizeInput($input) {
        return htmlspecialchars(trim($input));
    }

    # function for debugging purpose
    function logError($message) {
        error_log($message);
        $_SESSION['error'] = $message;
    }

    # check login request in post method
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        # display the error to inform the user
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        # check if the user selected a role, if not, redirect back to the index page
        if (!isset($_POST['role'])) {
            logError("Role not specified");
            header("Location: ../index.php");
            exit();
        }

        try {
            #
            $role = sanitizeInput($_POST['role']);

            # change the username and password name attributes based on the selected role from the index page dynamically
            # for username
            $username_field = match($role) {
                'teacher' => 'teacher_id', # teacher id for teachers
                'student' => 'student_lrn', # lrn for students
                'admin' => 'admin_id',
                default => null
            };

            # for password
            $password_field = match($role) {
                'teacher' => 'teacher_password',
                'student' => 'student_password',
                'admin' => 'admin_password',
                default => null
            };

            if (!$username_field || !$password_field) {
                logError("Invalid role specified");
                header("Location: index.php");
                exit();
            }

            # check if the username and password is present
            if (!isset($_POST[$username_field]) || !isset($_POST[$password_field])) {
                logError("Username or password not provided");
                header("Location: index.php");
                exit();
            }

            # sanitize username and password
            $username = sanitizeInput($_POST[$username_field]);
            $password = sanitizeInput($_POST[$password_field]);

            if (!$pdo) {
                throw new Exception("Database connection failed");
            }

            # prepare authentication query
            $query = $pdo->prepare("SELECT * FROM users WHERE username = ? AND user_role = ?");
            $query->execute([$username, $role]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            # verify password
            if ($user && password_verify($password, $user['password'])) {
                # get the id, username, user role and full name of the logged in user in the current session
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['full_name'] = $user['full_name'];
            
                # if the logged in user is a student, get the student id and store it
                if ($role === 'student') {
                    // Verify student record exists
                    $student = $pdo->prepare("SELECT id FROM students WHERE id = :student_id");
                    $student->execute([':student_id' => $user['student_id']]);
                    $studentRecord = $student->fetch(PDO::FETCH_ASSOC);
            
                    if (!$studentRecord) {
                        logError("No corresponding student record found");
                        session_destroy();
                        header("Location: ../index.php");
                        exit();
                    }
            
                    # add student_id to the session data too and check the debug_session.php file for debugging user data
                    $_SESSION['student_id'] = $user['student_id'];
                }            

                # redirect based on role
                switch ($role) {
                    case 'admin':
                        header("Location: ../admin/index.php");
                        break;
                    case 'teacher':
                        header("Location: ../teacher/index.php");
                        break;
                    case 'student':
                        header("Location: ../home.php");
                        break;
                    default:
                        logError("Invalid role after login");
                        header("Location: index.php");
                }
                exit();
            } else {
                # redirect back to the index page if login failed
                logError("Invalid credentials");
                header("Location: ../index.php");
                exit();
            }
        } catch (Exception $e) {
            logError("Login error: " . $e->getMessage());
            header("Location: ../index.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
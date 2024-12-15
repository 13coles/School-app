<?php
    function checkAccess($allowedRoles) {
        # check if the session has already started, start it if not
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        # check if the user is logged in
        if (!isset($_SESSION['user_role'])) {
            header("Location: ../index.php");
            exit();
        }

        # check if the current user's role is in the allowed roles
        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            # redirect to their index pages if they access routes/pages that is not for their role
            switch ($_SESSION['user_role']) {
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
                    header("Location: ../index.php");
                    break;
            }
            exit();
        }
    }
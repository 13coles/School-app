<?php
    session_start();
    require_once '../utils/db_connection.php';

    /**
     * Log user activities in the activy_logs table
     * @param int $user_id - pass the current logged in user's id from the session to the function
     * @param string $event - describe the type of event or action that the user performed
     * @return bool - returns true if logged successfully, false otherwise
     */
    function logActivity($user_id, $event) {
        global $pdo;
        
        try {
            $query = $pdo->prepare("
                INSERT INTO activity_logs (
                    user_id,
                    event
                ) VALUES (
                    :user_id,
                    :event
                )
            ");

            $result = $query->execute([
                ':user_id' => $user_id,
                ':event' => $event
            ]);

            return $result;

        } catch (Exception $e) {
            error_log("Error logging activity: " . $e->getMessage());
            return false;
        }
    }

    // Usage example:
    // logActivity($_SESSION['id'], "Added new teacher record for John Doe");
    // logActivity($_SESSION['id'], "Updated student attendance for Grade 6");
    // logActivity($_SESSION['id'], "Deleted class schedule for Section A");
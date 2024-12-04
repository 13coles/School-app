<?php
    session_start();

    require_once('./utils/db_connection.php');
    require_once('./utils/access_control.php');

    checkAccess(['student']);

    function sanitizeInput($input) {
        return htmlspecialchars(trim($input));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            # prepare the update query with all the fields that can be updated
            $updateFields = [
                'birth_date', 
                'religion', 
                'street', 
                'barangay', 
                'municipality', 
                'province', 
                'contact_number',
                'father_name',
                'mother_name',
                'guardian_name',
                'relationship',
                'guardian_contact'
            ];

            # build the SQL query dynamically
            $sql = "UPDATE students SET ";
            $updateValues = [];
            
            foreach ($updateFields as $field) {
                if (isset($_POST[$field]) && $_POST[$field] !== '') {
                    $sql .= "$field = :$field, ";
                    $updateValues[$field] = sanitizeInput($_POST[$field]);
                }
            }

            # remove the comma and white spaces
            $sql = rtrim($sql, ', ');

            # add the WHERE clause
            $sql .= " WHERE id = :student_id";
            $updateValues['student_id'] = $_SESSION['student_id'];

            $query = $pdo->prepare($sql);
            $query->execute($updateValues);

            # check if the update was successful
            if ($query->rowCount() > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Profile updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'info',
                    'message' => 'No changes were made to your profile.'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Method Not Allowed'
        ]);
    }
    exit;
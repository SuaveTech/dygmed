<?php

// Error reporting - only for development, disable in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import the database connection
include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $start_date = htmlspecialchars($_POST['start_date'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? '');
    $id = intval($_POST['id00'] ?? 0);

    // Error flag
    $error = '3';

    // Validate inputs
    if (!empty($start_date) && !empty($status) && $id > 0) {
        // Use prepared statements to prevent SQL injection
        $stmt = $database->prepare("UPDATE subscription SET start_date = ?, status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssi", $start_date, $status, $id);
            if ($stmt->execute()) {
                $error = '4'; // Update successful
            } else {
                $error = '3'; // Query execution failed
            }
            $stmt->close();
        } else {
            $error = '3'; // Query preparation failed
        }
    } else {
        $error = '3'; // Invalid input
    }
} else {
    $error = '3'; // Invalid request method
}

header("Location: subscriptions.php?action=edit&error=" . $error . "&id=" . $id);
exit();
?>

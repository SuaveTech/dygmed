<?php

// Error reporting - only for development, disable in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import the database connection
include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $name = htmlspecialchars($_POST['name'] ?? '');
    $price = htmlspecialchars($_POST['price'] ?? '');
    $duration = htmlspecialchars($_POST['duration'] ?? '');
    $id = intval($_POST['id00'] ?? 0);

    // Error flag
    $error = '3';

    // Validate inputs
    if (!empty($name) && !empty($price) && !empty($duration) && $id > 0) {
        // Use prepared statements to prevent SQL injection
        $stmt = $database->prepare("UPDATE plan SET name = ?, price = ?, duration = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("siii", $name, $price, $duration, $id);
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

header("Location: plans.php?action=edit&error=" . $error . "&id=" . $id);
exit();
?>

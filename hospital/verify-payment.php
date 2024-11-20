<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("../connection.php");

$reference = $_GET['reference'] ?? null;
$planId = $_GET['plan_id'] ?? null;

if (!$reference || !$planId) {
    die("No reference or plan ID supplied.");
}

$secretKey = "sk_test_88bdcabb6b395c12d8272a61020c0e95280028db";

// Retrieve hospital ID from session
$hospitalId = $_SESSION['id'] ?? null;
if (!$hospitalId) {
    die("Hospital ID not found in session.");
}


$planQuery = $database->prepare("SELECT price, duration FROM plan WHERE id = ?");
$planQuery->bind_param("i", $planId); 
$planQuery->execute();
$planQuery->store_result(); 


if ($planQuery->num_rows > 0) {
   
    $planQuery->bind_result($planPrice, $planDuration);
    $planQuery->fetch(); 
} else {
    die("Plan not found.");
}

$planAmount = $planPrice * 100; 
$planQuery->close(); 

// Verify payment with Paystack
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $secretKey]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die("Curl returned an error: " . curl_error($ch));
}
curl_close($ch);

$result = json_decode($response, true);


if ($result && $result["status"] && $result["data"]["status"] === "success" && $result["data"]["amount"] == $planAmount) {

    $status = 'active';
} else {
  
    $status = 'failed';
}

// Check if the reference already exists in the subscription table
$referenceCheckQuery = $database->prepare("SELECT id FROM subscription WHERE reference = ?");
$referenceCheckQuery->bind_param("s", $reference);
$referenceCheckQuery->execute();
$referenceCheckQuery->store_result();
if ($referenceCheckQuery->num_rows > 0) {
    die("This reference has already been used.");
}

$referenceCheckQuery->close();


$activeSubscriptionCheckQuery = $database->prepare("SELECT id FROM subscription WHERE hospital_id = ? AND status = 'active'");
$activeSubscriptionCheckQuery->bind_param("i", $hospitalId);
$activeSubscriptionCheckQuery->execute();
$activeSubscriptionCheckQuery->store_result();


if ($activeSubscriptionCheckQuery->num_rows > 0) {
    $status = 'pending';
}


$activeSubscriptionCheckQuery->close();


$insertQuery = $database->prepare(
    "INSERT INTO subscription (reference, plan_id, hospital_id, amount, duration, status, created_at, start_date) 
    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
);
$insertQuery->bind_param("siidss", $reference, $planId, $hospitalId, $planPrice, $planDuration, $status); 
$success = $insertQuery->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .message-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            width: 80%;
            max-width: 500px;
        }
        .message-container h1 {
            color: #4CAF50;
        }
        .message-container .success {
            color: #4CAF50;
        }
        .message-container .failed {
            color: #f44336;
        }
        .message-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .message-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="message-container">
    <?php if ($success && $status === 'active'): ?>
        <h1 class="success">Payment Successful!</h1>
        <p>Your subscription has been activated.</p>
    <?php elseif ($success): ?>
        <h1 class="success">Subscription Created!</h1>
        <p>Your subscription is pending activation.</p>
    <?php else: ?>
        <h1 class="failed">Payment Failed!</h1>
        <p>There was an issue with your payment. Please try again.</p>
    <?php endif; ?>

    <a href="subscription.php">Go Back to Subscriptions</a>
</div>

</body>
</html>

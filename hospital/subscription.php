<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">

    <title>Doctors</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }

        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>

    <style>
        /* Basic styling for grid and cards */
        .grid-container {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            padding: 20px;
        }

        .card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card h2 {
            font-size: 1.5em;
            color: #333;
        }

        .card p {
            font-size: 1.2em;
            color: #666;
        }
    </style>
</head>

<body>
    <?php

    //learn from w3schools.com

    session_start();

    if (isset($_SESSION["user"])) {
        if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'h') {
            header("location: ../login.php");
        }
    } else {
        header("location: ../login.php");
    }

        //import database
        include("../connection.php");

    $hospitalId = $_SESSION['id'];



        // Fetch user info
        $query = "SELECT name, email, profile_picture FROM hospital WHERE id='$hospitalId'";
        $result = $database->query($query);
        $user = $result->fetch_assoc();
        $absolute_path = $user['profile_picture'];
        $profile_parts = explode("/htdocs", $absolute_path);
        $relative_path = isset($profile_parts[1]) ? $profile_parts[1] : '../img/user.png'; 
        

        $profile_picture = $relative_path ?: '../img/user.png';




    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                        <tr>
                                <td width="30%" style="padding-left:20px">
                                <img src="<?php echo $profile_picture; ?>" class="profile-img" alt="User Image" id="profileImage">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                <p class="profile-title"><?php echo $user['name']; ?></p>
                                <p class="profile-subtitle"><?php echo $user['email']; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text">Dashboard</p>
                        </a>
        </div></a>
        </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-doctor">
                <a href="doctors.php" class="non-style-link-menu">
                    <div>
                        <p class="menu-text">Doctors</p>
                </a>
    </div>
    </td>
    </tr>
    <tr class="menu-row">
        <td class="menu-btn menu-icon-schedule">
            <a href="schedule.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Schedule</p>
                </div>
            </a>
        </td>
    </tr>
    <tr class="menu-row">
        <td class="menu-btn menu-icon-appoinment">
            <a href="appointment.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Appointment</p>
            </a></div>
        </td>
    </tr>
    <tr class="menu-row">
        <td class="menu-btn menu-icon-patient">
            <a href="patient.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Patients</p>
            </a></div>
        </td>
    </tr>
    <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment  menu-active menu-icon-appoinment-active">
                        <a href="subscription.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">Subscription</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>

    </table>
    </div>


    <?php
    session_start();
    include("../connection.php");

    $hospitalId = $_SESSION['id'] ?? null;
    if (!$hospitalId) {
        die("Hospital ID not found in session.");
    }



    // Get the current date for display
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');

    // Handle search functionality
    if ($_POST) {
        $keyword = $_POST["search"];
        $sqlmain = "SELECT s.*, p.plan_name FROM subscription s 
                INNER JOIN plan p ON s.plan_id = p.id 
                WHERE s.hospital_id = ? AND (s.docemail = ? OR s.docname = ? OR s.docname LIKE ? OR s.docname LIKE ? OR s.docname LIKE ?)";
    } else {
        // Fetch the subscriptions for the current hospital
        $sqlmain = "SELECT s.*, p.name FROM subscription s 
                INNER JOIN plan p ON s.plan_id = p.id 
                WHERE s.hospital_id = ? ORDER BY s.id DESC";
    }

    $stmt = $database->prepare($sqlmain);
    if ($_POST) {
        $stmt->bind_param("isssss", $hospitalId, $keyword, $keyword, $keyword . '%', '%' . $keyword, '%' . $keyword . '%');
    } else {
        $stmt->bind_param("i", $hospitalId);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="doctors.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                            <font class="tn-in-text">Back</font>
                        </button></a>
                </td>
                <td>
                    <h2 style="margin-left: 20px;">Subscriptions</h2>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                        Today's Date
                    </p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php echo $date; ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <a href="payment.php" class="non-style-link"><button class="login-btn btn-primary btn button-icon" style="display: flex;justify-content: center;align-items: center;margin-left:75px;background-image: url('../img/icons/add.svg');">Add New</button></a>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Subscriptions (<?php echo $result->num_rows; ?>)</p>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <center>
                        <div class="abc scroll">
                            <table width="93%" class="sub-table scrolldown" border="0">
                                <thead>
                                    <tr>
                                        <th class="table-headin">Month</th>
                                        <th class="table-headin">Plan</th>
                                        <th class="table-headin">Amount</th>
                                        <th class="table-headin">Status</th>
                                        <th class="table-headin">Payment Date</th>
                                        <th class="table-headin">Events</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows == 0) {
                                        echo '<tr>
                                        <td colspan="6">
                                            <br><br><br><br>
                                            <center>
                                            <img src="../img/notfound.svg" width="25%">
                                            <br>
                                            <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find any subscriptions.</p>
                                            <a class="non-style-link" href="doctors.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">Show All Subscriptions</button></a>
                                            </center>
                                            <br><br><br><br>
                                        </td>
                                    </tr>';
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            $planName = $row["name"]; // Get plan name from the plan table
                                            $amount = $row["amount"];
                                            $status = $row["status"];
                                            $reference = $row["reference"];
                                            $paymentDate = $row["payment_date"]; // Assuming a field like `payment_date` exists
                                            $createdAt = $row["created_at"];

                                            // Format date to show as month
                                            $month = date("F Y", strtotime($createdAt));
                                            $paymentDateFormatted = date("F d, Y", strtotime($paymentDate));

                                            echo '<tr>
                                            <td>' . $month . '</td>
                                            <td>' . $planName . '</td>
                                            <td>₦' . number_format($amount, 2) . '</td>
                                            <td>' . ucfirst($status) . '</td>
                                            <td>' . $paymentDateFormatted . '</td>
                                            <td>
                                                <div style="display:flex;justify-content: center;">
                                                    <a href="?action=view&id=' . $row["id"] . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                                </div>
                                            </td>
                                        </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </center>
                </td>
            </tr>
        </table>
    </div>



    </div>
    <?php
    if ($_GET) {

        $id = $_GET["id"];
        $action = $_GET["action"];

    
        if ($action == 'view') {
            // Query to get subscription details by ID
            $sqlmain = "SELECT s.*, p.name FROM subscription s 
                        INNER JOIN plan p ON s.plan_id = p.id 
                        WHERE s.id='$id'";  // Get subscription by ID along with plan name
            $result = $database->query($sqlmain);
            $row = $result->fetch_assoc();
            
            // Fetching subscription details
            $amount = $row["amount"];
            $status = $row["status"];
            $plan_name = $row["name"];
            $hospital_id = $row["hospital_id"];
            $start_date = $row["start_date"];
            $end_date = $row["end_date"];
        
            // Formatting the status to make it more readable
            $status_text = $status === 'active' ? 'Active' : ($status === 'pending' ? 'Pending' : 'Inactive');
            
            // Fetching hospital name or other details (if needed) for display
            $hospital_res = $database->query("SELECT name FROM hospital WHERE id='$hospital_id'");
            $hospital_data = $hospital_res->fetch_assoc();
            $hospital_name = $hospital_data['name'];
        
            // Echoing the popup with subscription details
            echo '
            <div id="popup1" class="overlay">
                <div class="popup"  style="max-height: 80vh; overflow-y: auto;">
                    <center>
                        <h2>Subscription Details</h2>
                        <a class="close" href="subscription.php">&times;</a>
                        <div class="content">
                            eDoc Web App<br>
                        </div>
                        <div style="display: flex; justify-content: center;">
                            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                    <td>
                                        <p style="padding: 0; margin: 0; text-align: left; font-size: 25px; font-weight: 500;">View Subscription Details.</p><br><br>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="plan_name" class="form-label">Plan Name: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        ' . $plan_name . '<br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="amount" class="form-label">Amount: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        ' . $amount . '<br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="status" class="form-label">Status: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        ' . $status_text . '<br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="start_date" class="form-label">Start Date: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        ' . $start_date . '<br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="end_date" class="form-label">End Date: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        ' . $end_date . '<br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="hospital_name" class="form-label">Hospital Name: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        ' . $hospital_name . '<br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <a href="subscription.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>
            ';
        }
    };

    ?>
    </div>

</body>

</html>
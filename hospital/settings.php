<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">

    <title>Hospital | Settings</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }

        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>

<body>
    <?php

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


    $hospital_id = $_SESSION["id"];
 
        // Fetch user info
        $query = "SELECT name, email, profile_picture FROM hospital WHERE id='$hospital_id'";
        $result = $database->query($query);
        $user = $result->fetch_assoc();
        $absolute_path = $user['profile_picture'];
        $profile_parts = explode("/htdocs", $absolute_path); // Split at "htdocs"
        $relative_path = isset($profile_parts[1]) ? $profile_parts[1] : '../img/user.png'; // Keep part after "htdocs"
        
        // Set default if the path is not available
        $profile_picture = $relative_path ?: '../img/user.png';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // File upload setup
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/medical/uploads/profile_pictures/";
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $file_name; // Generate unique filename
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image file
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
            $uploadOk = 0;
        }

        // Allow only specific image formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Limit file size (e.g., 2MB max)
        if ($_FILES["profile_picture"]["size"] > 2000000) {
            $error = "Your file is too large.";
            $uploadOk = 0;
        }

        // Attempt to upload file if all checks are passed
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Insert user data including profile picture path into database
                $sql = "UPDATE hospital SET profile_picture = '$target_file' WHERE id = '$hospital_id'";

                if ($database->query($sql) === TRUE) {
                    $success = "Profile picture updated successfully!";
                } else {
                    $error = "Database error: " . $database->error;
                }
            } else {
                $error = "Error uploading your file.";
            }
        }

        header("location: index.php");
    }



    ?>

<?php 
    $sql = "SELECT * FROM subscription WHERE hospital_id = ? AND status = 'active'";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // No active subscription found, display overlay
        echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Subscription Required</title>
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
            }
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            }
            .overlay-content {
                text-align: center;
            }
            .overlay button {
                margin-top: 20px;
                padding: 10px 20px;
                background-color: #007BFF;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .overlay button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="overlay">
            <div class="overlay-content">
                <h1>Subscription Required</h1>
                <p>You need an active subscription to access this page.</p>
                <button onclick="location.href='subscription.php';">Subscribe Now</button>
            </div>
        </div>
    </body>
    </html>
    HTML;
        exit;
    }

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
            <td class="menu-btn menu-icon-doctor ">
                <a href="doctors.php" class="non-style-link-menu ">
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
        <td class="menu-btn menu-icon-patient ">
            <a href="patient.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Patients</p>
            </a></div>
        </td>
    </tr>
    <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="subscription.php" class="non-style-link-menu"><div><p class="menu-text">Subscription</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings   menu-active menu-icon-settings-active">
                        <a href="settings.php" class="non-style-link-menu   non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>

    </table>
    </div>
    <div class="dash-body">

        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
            <tr>
                <td width="13%">

                    <a href="patient.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                            <font class="tn-in-text">Back</font>
                        </button></a>

                </td>
                <td>
                    <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Settings</p>

                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                        Today's Date
                    </p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php
                        date_default_timezone_set('Asia/Kolkata');

                        $date = date('Y-m-d');
                        echo $date;
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>


            </tr>
        </table>

        <?php if (!empty($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php elseif (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div style="padding: 20px;">
            <!-- Profile upload form -->
            <form action="" method="POST" enctype="multipart/form-data">


                <!-- Profile picture input -->
                <label for="profile_picture">Upload Profile Picture:</label><br><br>
                <input type="file" name="profile_picture" class="input-text" id="profile_picture" accept="image/*" required>

                <button type="submit" class="btn btn-primary" style="margin-top: 20px;" >Submit</button>
            </form>
        </div>

    </div>
    </div>

    </div>

</body>

</html>
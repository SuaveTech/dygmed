<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">

    <title>subscriptions</title>
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
        if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'a') {
            header("location: ../login.php");
        }
    } else {
        header("location: ../login.php");
    }



    //import database
    include("../connection.php");


    // Get the current date for display
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');


    if ($_POST) {
        $keyword = $_POST["search"];
        $sqlmain = "SELECT s.*, p.plan_name FROM subscription s 
                INNER JOIN plan p ON s.plan_id = p.id 
                WHERE (s.docemail = ? OR s.docname = ? OR s.docname LIKE ? OR s.docname LIKE ? OR s.docname LIKE ?)";
    } else {
        $sqlmain = "SELECT s.*, p.name FROM subscription s 
                INNER JOIN plan p ON s.plan_id = p.id 
                ORDER BY s.id DESC";
    }

    $stmt = $database->prepare($sqlmain);

    if ($_POST) {
        $stmt->bind_param("sssss", $keyword, $keyword, $keyword . '%', '%' . $keyword, '%' . $keyword . '%');
    }

    $stmt->execute();
    $result = $stmt->get_result();




    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@dygmed.com</p>
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
                            <a href="hospitals.php" class="non-style-link-menu">
                                <div>
                                    <p class="menu-text">Hospitals</p>
                            </a>
                    </div>
                    </td>
  
  
  
                </tr>

    <tr class="menu-row">
        <td class="menu-btn menu-icon-doctor">
            <a href="plans.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Plans</p>
            </a>
            </div>
        </td>
    </tr>

    <tr class="menu-row">
        <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
            <a href="subscriptions.php" class="non-style-link-menu non-style-link-menu-active">
                <div>
                    <p class="menu-text">Subscriptions</p>
            </a></div>
        </td>
    </tr>


    <tr class="menu-row">
        <td class="menu-btn menu-icon-doctor">
            <a href="settings.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Settings</p>
            </a></div>
        </td>
    </tr>




    </table>

    </div>

    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing: 0; margin:0; padding:0; margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="subscriptions.php">
                        <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px; padding-bottom:11px; margin-left:20px; width:125px">
                            <font class="tn-in-text">Back</font>
                        </button>
                    </a>
                </td>
                <td>
                    <form action="" method="post" class="header-search">

                        <?php
                        echo '<datalist id="subscriptions">';
                        $list11 = $database->query("SELECT amount, status FROM subscription;");
                        echo ' </datalist>';
                        ?>

                    </form>
                </td>
                <td width="15%">
                    <p style="font-size: 14px; color: rgb(119, 119, 119); padding: 0; margin: 0; text-align: right;">Today's Date</p>
                    <p class="heading-sub12" style="padding: 0; margin: 0;">
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        $date = date('Y-m-d');
                        echo $date;
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display: flex; justify-content: center; align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>

            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12" style="margin-left: 45px; font-size:18px; color:rgb(49, 49, 49)">
                        All Subscriptions (<?php echo $list11->num_rows; ?>)
                    </p>
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
                                            $planName = $row["name"];
                                            $id = $row["id"];
                                            $amount = $row["amount"];
                                            $status = $row["status"];
                                            $reference = $row["reference"];
                                            $paymentDate = $row["created_at"];
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
                                                   <a href="?action=edit&id=' . $id . '&error=0" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-edit"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Edit</font></button></a>
                                        &nbsp;&nbsp;&nbsp;
                                                     <a href="?action=view&id=' . $id . '" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                       &nbsp;&nbsp;&nbsp;
                                       <a href="?action=drop&id=' . $id . '" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>
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
        if ($action == 'drop') {

            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="subscriptions.php">&times;</a>
                        <div class="content">
                            You want to delete this subscription
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-subscription.php?id=' . $id . '" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="subscriptions.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            ';
        } elseif ($action == 'view') {
            $sqlmain = "select * from subscription where id='$id'";
            $result = $database->query($sqlmain);
            $row = $result->fetch_assoc();
            $status = $row["status"];
            $duration = $row["duration"];

            $amount = $row['amount'];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="subscriptions.php">&times;</a>
                        <div class="content">
                            eDoc Web App<br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Subscription Details.</p><br><br>
                                </td>
                            </tr>
                            
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $status . '<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $duration . ' months<br><br>
                                </td>
                            </tr>
                  
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Telephone: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $amount . '<br><br>
                                </td>
                            </tr>
                          
                            <tr>
                                <td colspan="2">
                                    <a href="subscriptions.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                
                                    
                                </td>
                
                            </tr>
                           

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';
        } elseif ($action == 'edit') {
            $sqlmain = "select * from subscription where id='$id'";
            $result = $database->query($sqlmain);
            $row = $result->fetch_assoc();
            $start_date = $row["start_date"];
            $status = $row["status"];

            $error_1 = $_GET["error"];
            $errorlist = array(

                '3' => '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                '4' => "",
                '0' => '',

            );

            if ($error_1 != '4') {
                echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            
                                <a class="close" href="subscriptions.php">&times;</a> 
                                <div style="display: flex;justify-content: center;">
                                <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                        <td class="label-td" colspan="2">' .
                    $errorlist[$error_1]
                    . '</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Subscription Details.</p>
                                        Subscription ID : ' . $id . '<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-subscription.php" method="POST" class="add-new-form">
                                            <label for="status" class="form-label">status: </label>
                                            <input type="hidden" value="' . $id . '" name="id00">
                                            <input type="hidden" name="status" value="' . $status . '" >
                                        </td>
 <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="status" class="form-label">Status:</label>
                                            <select name="status" class="input-text" required>
                                                <option value="" disabled ' . (empty($status) ? 'selected' : '') . '>Select Status</option>
                                                <option value="active" ' . ($status === 'active' ? 'selected' : '') . '>Active</option>
                                                <option value="inactive" ' . ($status === 'inactive' ? 'selected' : '') . '>Inactive</option>
                                                <option value="pending" ' . ($status === 'pending' ? 'selected' : '') . '>Pending</option>
                                                <option value="expired" ' . ($status === 'expired' ? 'selected' : '') . '>Expired</option>
                                            </select>
                                        </td>
                                    </tr>





                                    <tr>
                                        
                                        <td class="label-td" colspan="2">
                                            <label for="start_date" class="form-label">Start date: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="start_date" class="input-text" placeholder="Start Date" value="' . $start_date . '" required><br>
                                        </td>
                                        
                                    </tr>
               
                        
                                    <tr>
                                        <td colspan="2">
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                            <input type="submit" value="Save" class="login-btn btn-primary btn">
                                        </td>
                        
                                    </tr>
                                
                                    </form>
                                    </tr>
                                </table>
                                </div>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>
                    ';
            } else {
                echo '
                <div id="popup1" class="overlay">
                        <div class="popup">
                        <center>
                        <br><br><br><br>
                            <h2>Edit Successfully!</h2>
                            <a class="close" href="subscriptions.php">&times;</a>
                            <div class="content">
                                
                                
                            </div>
                            <div style="display: flex;justify-content: center;">
                            
                            <a href="subscriptions.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>

                            </div>
                            <br><br>
                        </center>
                </div>
                </div>
    ';
            };
        };
    };

    ?>
    </div>

</body>

</html>
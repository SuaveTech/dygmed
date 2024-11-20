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

    $plans = $database->query("SELECT id, name, price, duration FROM plan");


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
            <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
                <a href="doctors.php" class="non-style-link-menu non-style-link-menu-active">
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
    <tr class="menu-row">
        <td class="menu-btn menu-icon-cog">
            <a href="settings.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text">Settings</p>
            </a></div>
        </td>
    </tr>

    </table>
    </div>

    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="subscription.php">
                        <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                            <font class="tn-in-text">Back</font>
                        </button>
                    </a>
                </td>
                <td>
                    <h2 style="margin-left: 20px;">Subscription</h2>
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
                    <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                        <img src="../img/calendar.svg" width="100%">
                    </button>
                </td>
            </tr>
        </table>

        <h1 style="text-align: center; margin-top: 20px;">Available Plans</h1>

        <div class="grid-container">
            <?php
            $paystackPublicKey = "pk_test_3f58bc8fc39d9fb757b796b969c0653ecd3e016f";
            $email = $_SESSION["user"];
    

            foreach ($plans as $plan):
                $amountKobo = $plan['price'] * 100; // Convert to kobo for Paystack
                $planId = $plan['id'];
            ?>
                <div class="card">
                    <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                    <p>Price: ₦<?php echo number_format($plan['price'], 2); ?></p>
                    <p>Duration: <?php echo htmlspecialchars($plan['duration']); ?></p>
                    <button class="subscribe-btn" onclick="payWithPaystack('<?php echo $paystackPublicKey; ?>', '<?php echo $email; ?>', <?php echo $amountKobo; ?>, <?php echo $planId; ?>)">Subscribe</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Paystack Script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack(publicKey, email, amount, planId) {
            var handler = PaystackPop.setup({
                key: publicKey,
                email: email,
                amount: amount, // Amount in kobo
                currency: 'NGN',
                callback: function(response) {
                    window.location.href = 'verify-payment.php?reference=' + response.reference + '&plan_id=' + planId;
                },
                onClose: function() {
                    alert('Transaction was not completed.');
                }
            });
            handler.openIframe();
        }
    </script>

    <style>
        .grid-container {
            display: grid;
            gap: 20px;
            margin-top: 20px;
            padding: 0 20px;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .card {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h2 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 1em;
            color: #555;
            margin: 5px 0;
        }

        .subscribe-btn {
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .subscribe-btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .grid-container {
                grid-template-columns: 1fr;
            }

            .card h2 {
                font-size: 1.3em;
            }

            .card p {
                font-size: 0.9em;
            }

            .subscribe-btn {
                font-size: 0.9em;
            }
        }
    </style>



    </div>

    </div>

</body>

</html>
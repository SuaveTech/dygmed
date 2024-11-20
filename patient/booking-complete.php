<?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $userrow = $database->query("select * from patient where pemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    // if($_POST){
    //     if(isset($_POST["booknow"])){
    //         $apponum=$_POST["apponum"];
    //         $scheduleid=$_POST["scheduleid"];
    //         $date=$_POST["date"];
    //         $scheduleid=$_POST["scheduleid"];
    //         $sql2="insert into appointment(pid,apponum,scheduleid,appodate) values ($userid,$apponum,$scheduleid,'$date')";
    //         $result= $database->query($sql2);
    //         //echo $apponom;
    //         header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");

    //     }
    // }

    if ($_POST) {
        if (isset($_POST["booknow"])) {
            $apponum = $_POST["apponum"];
            $scheduleid = $_POST["scheduleid"];
            $date = $_POST["date"];
            $note = $_POST["note"] ?? ''; // Retrieve the note
            $attachment = $_FILES["attachment"]; // Retrieve the uploaded file

            $hospital_id = $_SESSION['hospital_id'];
            
            // Handle file upload if attachment is provided
            $attachment_path = null;
            if ($attachment['error'] == 0) {
                $target_dir = "../uploads/attachments/";
                $target_file = $target_dir . basename($attachment["name"]);

                echo "File Type: " . $attachment["type"] . "<br>";
echo "File Size: " . $attachment["size"] . "<br>";
                
                // Check file type and size (optional)
                $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
                $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                if ($attachment["size"] <= 2000000 && in_array($file_extension, $allowed_types)) {
                    if (move_uploaded_file($attachment["tmp_name"], $target_file)) {
                        $attachment_path = $target_file; // Save the path if upload is successful
                    } else {
                        die("Error uploading file.");
                    }
                } else {
                    die("File type not allowed or file is too large.");
                }
            }
            
            // Insert into database, including note and attachment path
            $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate, note, attachment, hospital_id) 
                     VALUES ($userid, $apponum, $scheduleid, '$date', '$note', '$attachment_path', '$hospital_id' )";
            
            if ($database->query($sql2)) {
                header("Location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");
            } else {
                echo "Error: " . $database->error;
            }
        }
    }
    
 ?>
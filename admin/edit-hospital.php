<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//import database
include("../connection.php");



if ($_POST) {
    //print_r($_POST);
    $result = $database->query("select * from webuser");
    $name = $_POST['name'];
    $oldemail = $_POST["oldemail"];
    $email = $_POST['email'];
    $tele = $_POST['phone'];
    $id = $_POST['id00'];

        $error = '3';
        $result = $database->query("select hospital.id from hospital inner join webuser on hospital.email=webuser.email where webuser.email='$email';");
  
        if ($result->num_rows == 1) {
            $id2 = $result->fetch_assoc()["id"];
        } else {
            $id2 = $id;
        }

        // echo $id2 . "jdfjdfdh";
        if ($id2 != $id) {
            $error = '1';
        } else {

            $sql1 = "update hospital set email='$email',name='$name',phone='$tele' where id=$id ;";
            $database->query($sql1);

            $sql1 = "update webuser set email='$email' where email='$oldemail' ;";
            $database->query($sql1);
         
            $error = '4';
        }

} else {
    //header('location: signup.php');
    $error = '3';
}


header("location: hospitals.php?action=edit&error=" . $error . "&id=" . $id);
?>
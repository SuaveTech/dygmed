<?php

    $database= new mysqli("localhost","root","","dy");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>
<?php

$db_name = "mysql:host=localhost;dbname=user_form";
$username = "root";
$password = "";

$conn = new PDO($db_name, $username, $password);

ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );

<?php 

$host = "localhost";
$user = "root";
$password = "";
$database = "xobo-file-system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("connection failed". $conn->connect_error);
}

?>
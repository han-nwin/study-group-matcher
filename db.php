<?php
$servername = "mysql";
$username = "root";
$password = "";
$dbname = "study_group_matcher";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


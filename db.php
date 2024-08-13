<?php
$servername = "localhost";
$username = "jihan121";
$password = "jihan121*";
$database = "login";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("gagal mengoneksi: " . $conn->connect_error);
}
<?php

$conn = new mysqli('localhost', 'root', '', 'SneakPeak', 3306);

if (!$conn) {   
    die("Connection failed: " . mysqli_connect_error());
}
?>
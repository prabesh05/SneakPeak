<?php

$conn = mysqli_connect('localhost', 'root', '', 'SneakPeak');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
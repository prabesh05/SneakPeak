<?php
session_start();
include "database.php";

if (isset($_POST['login'])) {
    
$username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM register WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {

            $_SESSION['username'] = $username;
            $_SESSION['email']    = $row['email'];

            header("Location: products.php");
            exit;

        } else {

            echo "<p style='color:red;'>Incorrect password. Please try again.</p>";

        }

    } else {

        echo "<p style='color:red;'>No account found with that username.</p>"; // Fix

    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SneakPeak</title>
</head>
<body>

    <h2>Login</h2>

    <form id="loginForm" method="POST">

        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="login">Login</button>

    </form>

    <p>Don't have an account?
        <a href="register.php">Register here</a>
    </p>

</body>
</html>
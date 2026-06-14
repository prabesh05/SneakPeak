<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>

    <h2>Register</h2>

    <form id="registrationForm" method="POST">

        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="register">Register</button>  <!-- Fix 1 -->

    </form>

    <p>Already have an account?
        <a href="login.php">Login here</a>
    </p>

</body>
</html>

<?php
include "database.php";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);  
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  

    $sql = "INSERT INTO register (username, email, password) VALUES ('$username', '$email', '$password')";

    if (mysqli_query($conn, $sql) === TRUE) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn); // Fix 2
}
?>
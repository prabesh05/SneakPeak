<?php
include "database.php";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($email == "admin1@sneakpeak.np" || $email == "admin2@sneakpeak.np" || $email == "owner@sneakpeak.np") {
        $role = "admin";
    } else {
        $role = "user";
    }

    $sql = "INSERT INTO login (user_name, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

    if (mysqli_query($conn, $sql) === TRUE) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

    
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<div class="container">

<h2>Register</h2>

<form id="registrationForm" method="POST">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" name="register">Register</button>

</form>

<p>
    Already have an account?
    <a href="login.php">Login here</a>
</p>

</div>

</body>
</html>


<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

:root{
    --red:#E8192C;
    --black:#111111;
    --white:#F5F5F5;
    --grey:#999;
}

body{
    font-family:'Barlow',sans-serif;
    background:var(--black);
    color:var(--white);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    overflow:hidden;
    position:relative;
}

/* Background Glow */
body::before{
    content:"";
    position:absolute;
    width:500px;
    height:500px;
    background:radial-gradient(circle, rgba(232,25,44,.25), transparent 70%);
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    z-index:0;
}

/* Registration Card */
.container{
    position:relative;
    z-index:1;
    width:400px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    padding:40px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.5);
}

h2{
    text-align:center;
    font-family:'Bebas Neue',sans-serif;
    font-size:48px;
    color:var(--red);
    letter-spacing:2px;
    margin-bottom:25px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

input{
    width:100%;
    padding:14px;
    margin-bottom:20px;
    border:none;
    outline:none;
    border-radius:10px;
    background:#222;
    color:var(--white);
    font-size:16px;
    transition:.3s;
}

input:focus{
    border:2px solid var(--red);
    box-shadow:0 0 15px rgba(232,25,44,.4);
}

button{
    width:100%;
    padding:15px;
    border:none;
    border-radius:50px;
    background:var(--red);
    color:white;
    font-family:'Barlow Condensed',sans-serif;
    font-size:18px;
    font-weight:700;
    letter-spacing:2px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 30px rgba(232,25,44,.5);
}

p{
    text-align:center;
    margin-top:20px;
    color:var(--grey);
}

a{
    color:var(--red);
    text-decoration:none;
    font-weight:600;
}

a:hover{
    text-decoration:underline;
}

@media(max-width:480px){
    .container{
        width:90%;
        padding:30px;
    }

    h2{
        font-size:40px;
    }
}
</style>
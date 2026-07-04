<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM login WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == "admin") {
                header("Location: adminProduct.php");
                exit;
            } else {
                header("Location: products.php");
                exit;
            }
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "No account found with that email";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<div class="container">

<h2>Login</h2>

<?php if (isset($error)) { ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php } ?>

<form method="POST">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" name="login">Login</button>

</form>

<p>
    Don't have an account?
    <a href="register.php">Register here</a>
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

/* glowing background */
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

/* container */
.container{
    position:relative;
    z-index:1;
    width:380px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    padding:40px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.5);
}

/* title */
h2{
    text-align:center;
    font-family:'Bebas Neue',sans-serif;
    font-size:48px;
    color:var(--red);
    letter-spacing:2px;
    margin-bottom:25px;
}

/* error message */
.error{
    background:rgba(255,0,0,.1);
    color:#ff4d4d;
    padding:10px;
    text-align:center;
    margin-bottom:15px;
    border-radius:10px;
    font-weight:600;
}

/* labels */
label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

/* inputs */
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

/* button */
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

/* bottom text */
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

/* mobile */
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
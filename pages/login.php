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
    <div class="password-wrap">
        <input type="password" id="password" name="password" required>
        <span class="toggle-pw" id="togglePw" tabindex="0">&#128065;</span>
    </div>

    <button type="submit" name="login">Login</button>

</form>

<p>
    Don't have an account?
    <a href="register.php">Register here</a>
</p>

</div>

<script>
document.getElementById('togglePw').addEventListener('click', function(){
    var pw = document.getElementById('password');
    if (pw.type === 'password') { pw.type = 'text'; } else { pw.type = 'password'; }
});
</script>
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
    --card:#161616;
    --white:#F5F5F5;
    --grey:#8a8a8a;
    --line:rgba(255,255,255,.08);
}

html,body{ height:100%; }

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

/* grain texture, matches the admin panel */
body::before{
    content:"";
    position:fixed;
    inset:0;
    background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
    opacity:.035;
    pointer-events:none;
    z-index:0;
}

/* diagonal glow beam instead of a centered blur blob */
body::after{
    content:"";
    position:fixed;
    width:900px;
    height:280px;
    background:linear-gradient(100deg, rgba(232,25,44,.22), transparent 65%);
    top:-60px;
    right:-200px;
    transform:rotate(18deg);
    z-index:0;
    pointer-events:none;
}

/* container */
.container{
    position:relative;
    z-index:1;
    width:400px;
    background:var(--card);
    border:1px solid var(--line);
    border-left:5px solid var(--red);
    padding:46px 42px 40px;
    border-radius:4px;
    box-shadow:0 25px 60px rgba(0,0,0,.55);
}


.container::before{
    position:absolute;
    top:0;
    right:0;
    background:var(--red);
    color:var(--black);
    font-family:'Barlow Condensed',sans-serif;
    font-weight:800;
    font-size:11px;
    letter-spacing:.2em;
    padding:5px 14px 5px 18px;
    clip-path:polygon(14px 0, 100% 0, 100% 100%, 0 100%);
}

/* title */
h2{
    font-family:'Bebas Neue',sans-serif;
    font-size:46px;
    letter-spacing:.03em;
    color:var(--white);
    margin-bottom:6px;
    line-height:.95;
}

h2::after{
    content:"";
    display:block;
    width:56px;
    height:4px;
    background:var(--red);
    margin-top:14px;
    margin-bottom:24px;
}

/* error message */
.error{
    background:rgba(232,25,44,.1);
    border:1px solid rgba(232,25,44,.35);
    color:#ff5a5a;
    padding:11px 14px;
    margin-bottom:18px;
    border-radius:4px;
    font-size:14px;
    font-weight:600;
}

/* labels */
label{
    display:block;
    margin-bottom:8px;
    font-family:'Barlow Condensed',sans-serif;
    font-weight:700;
    font-size:12px;
    letter-spacing:.16em;
    text-transform:uppercase;
    color:var(--grey);
}

/* underline-style inputs, no boxes */
input{
    width:100%;
    padding:12px 4px;
    margin-bottom:24px;
    border:none;
    border-bottom:2px solid var(--line);
    outline:none;
    border-radius:0;
    background:transparent;
    color:var(--white);
    font-size:16px;
    transition:border-color .25s, box-shadow .25s;
}

input::placeholder{ color:#555; }

input:focus{
    border-bottom-color:var(--red);
    box-shadow:0 6px 14px -8px rgba(232,25,44,.6);
}

/* password wrapper reuses the same underline treatment */
.password-wrap{
    position:relative;
    width:100%;
    margin-bottom:0;
}

.password-wrap input{
    padding-right:38px;
    margin-bottom:24px;
}

.toggle-pw{
    position:absolute;
    right:4px;
    top:14px;
    cursor:pointer;
    user-select:none;
    font-size:17px;
    line-height:1;
    color:var(--grey);
    transition:color .2s;
}

.toggle-pw:hover{ color:var(--white); }

/* button, sharp with a clipped corner like a shoebox label */
button{
    width:100%;
    padding:16px;
    margin-top:6px;
    border:2px solid var(--red);
    border-radius:4px;
    background:var(--red);
    color:var(--black);
    font-family:'Barlow Condensed',sans-serif;
    font-size:16px;
    font-weight:800;
    letter-spacing:.18em;
    text-transform:uppercase;
    cursor:pointer;
    clip-path:polygon(0 0, calc(100% - 16px) 0, 100% 100%, 0 100%);
    transition:background .25s, color .25s, transform .2s;
}

button:hover{
    background:transparent;
    color:var(--red);
    transform:translateY(-2px);
}

/* bottom text */
p{
    text-align:center;
    margin-top:26px;
    color:var(--grey);
    font-size:14px;
}

a{
    color:var(--red);
    text-decoration:none;
    font-weight:700;
}

a:hover{ text-decoration:underline; }

/* mobile */
@media(max-width:480px){
    .container{
        width:90%;
        padding:34px 26px;
    }

    h2{ font-size:38px; }
}
</style>
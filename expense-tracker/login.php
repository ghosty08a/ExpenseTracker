<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div id="particles-js"></div>
<div class="auth-box">
    <h1>🔐 Login</h1>

    <form method="POST" action="auth.php">

        <div class="input-group">
            <input type="text" name="username" required>
            <label>Username</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <button type="submit">Login</button>
    </form>

    <p style="margin-top:15px;">
        Don't have an account? <a href="register.php">Register</a>
    </p>
</div>
<script>
particlesJS("particles-js", {
  particles: {
    number: { value: 60 },
    size: { value: 3 },
    move: { speed: 2 },
    line_linked: { enable: true },
  }
});
</script>
</body>
</html>
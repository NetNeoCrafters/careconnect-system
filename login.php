<?php
session_start();
include("includes/db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input    = trim($_POST['email']);  // can be email or Admin
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM User WHERE email = ? OR name = ?");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role']    = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - CareConnect</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdfaf5;
            margin: 0;
        }

        .navbar {
            background-color: #800000;
            color: white;
            padding: 15px 20px;
        }

        .container {
            max-width: 450px;
            margin: 60px auto;
            background: #fffbe6;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #800000;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            margin-bottom: 18px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            background-color: #800000;
            color: #FFD700;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .message.error { color: red; }

        .footer-link {
            text-align: center;
            margin-top: 20px;
        }

        .footer-link a {
            color: #800000;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <strong>CareConnect Login</strong>
</div>

<div class="container">
    <h2>Welcome Back</h2>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="email" placeholder="Email or Username" required>
        <input type="password" name="password" placeholder="Password" required minlength="6">
        <button type="submit">Login</button>
    </form>

    <div class="footer-link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</div>

</body>
</html>

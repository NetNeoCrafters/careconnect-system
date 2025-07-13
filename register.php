<?php
include("includes/db.php");

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM User WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Insert new user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO User (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed, $role);
            if ($stmt->execute()) {
                $success = "Registration successful. Please login.";
                header("Refresh:2; url=login.php");
            } else {
                $error = "Something went wrong during registration.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - CareConnect</title>
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
        input, select {
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
        .message.success { color: green; }
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
    <strong>CareConnect Registration</strong>
</div>

<div class="container">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>

        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="donor">Donor</option>
            <option value="orphanage">Orphanage</option>
        </select>

        <input type="password" name="password" placeholder="Password" required minlength="6">
        <input type="password" name="confirm" placeholder="Confirm Password" required minlength="6">

        <button type="submit">Register</button>
    </form>

    <div class="footer-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

</body>
</html>

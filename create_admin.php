<?php
include("includes/db.php");

// Check if Admin already exists
$stmt = $conn->prepare("SELECT * FROM User WHERE name = 'Admin' OR email = 'Admin'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Generate secure hash of Admin123
    $hashed = password_hash("Admin123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO User (name, email, password, role) VALUES (?, ?, ?, ?)");
    $name = "Admin";
    $email = "Admin";
    $role = "admin";
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);

    if ($stmt->execute()) {
        echo "<h3 style='color:green;'>✅ Admin account created successfully.</h3>";
    } else {
        echo "<h3 style='color:red;'>❌ Failed to create admin.</h3>";
    }
} else {
    echo "<h3 style='color:orange;'>⚠️ Admin already exists. No action taken.</h3>";
}
?>

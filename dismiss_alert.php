<?php
include("includes/db.php");
include("includes/auth.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alert_key'])) {
    $user_id = $_SESSION['user_id'];
    $alert_key = $_POST['alert_key'];

    $stmt = $conn->prepare("REPLACE INTO AlertDismissals (user_id, alert_key, dismissed_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $alert_key);
    $stmt->execute();
    echo "OK";
} else {
    http_response_code(400);
    echo "Invalid request";
}

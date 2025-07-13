<?php
include("includes/db.php");
include("includes/auth.php");

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Handle join request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_post_id'])) {
    $post_id = intval($_POST['join_post_id']);
    $today = date("Y-m-d");

    // Check if already joined
    $check = $conn->prepare("SELECT * FROM EventJoin WHERE user_id = ? AND post_id = ?");
    $check->bind_param("ii", $user_id, $post_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO EventJoin (user_id, post_id, request_date) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $user_id, $post_id, $today);
        $insert->execute();
        $join_success = "You have successfully requested to join the event.";
    } else {
        $join_error = "You have already requested to join this event.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>News & Events - CareConnect</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdfaf5;
            margin: 0;
        }

        .navbar {
            background-color: #800000;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #FFD700;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fffbe6;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #800000;
        }

        .post {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .post h4 {
            margin: 0 0 5px;
            color: #800000;
        }

        .post small {
            color: #666;
        }

        .post img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 6px;
        }

        .post p {
            margin-top: 12px;
        }

        .join-btn {
            display: inline-block;
            background-color: #800000;
            color: #FFD700;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            margin-top: 12px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>CareConnect</strong></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Latest News & Events from Orphanages</h2>

    <?php if (isset($join_success)): ?>
        <div class="message" style="color:green;"><?php echo $join_success; ?></div>
    <?php elseif (isset($join_error)): ?>
        <div class="message" style="color:red;"><?php echo $join_error; ?></div>
    <?php endif; ?>

    <?php
    $stmt = $conn->query("
        SELECT Post.*, Orphanage.name AS orphanage_name 
        FROM Post 
        JOIN Orphanage ON Post.orphanage_id = Orphanage.orphanage_id 
        ORDER BY Post.date_posted DESC
    ");

    while ($row = $stmt->fetch_assoc()) {
        $already_joined = false;

        if ($role === 'donor') {
            $check = $conn->prepare("SELECT * FROM EventJoin WHERE user_id = ? AND post_id = ?");
            $check->bind_param("ii", $user_id, $row['post_id']);
            $check->execute();
            $check_result = $check->get_result();
            $already_joined = ($check_result->num_rows > 0);
        }

        echo "<div class='post'>";
        echo "<h4>" . htmlspecialchars($row['title']) . "</h4>";
        echo "<small>Posted by: <strong>" . htmlspecialchars($row['orphanage_name']) . "</strong></small><br>";
        echo "<small>Date Posted: " . $row['date_posted'] . "</small><br>";
        if (!empty($row['event_date'])) {
            echo "<small>Event Date: " . $row['event_date'] . "</small><br>";
        }
        if (!empty($row['post_image']) && file_exists("uploads/posts/{$row['post_image']}")) {
            echo "<img src='uploads/posts/{$row['post_image']}' alt='Post Image'>";
        }
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";

        if ($role === 'donor') {
            if ($already_joined) {
                echo "<div class='message' style='color:green;'>You have already requested to join this event.</div>";
            } else {
                echo "<form method='post'>
                    <input type='hidden' name='join_post_id' value='{$row['post_id']}'>
                    <button class='join-btn' type='submit'>Request to Join Event</button>
                </form>";
            }
        }

        echo "</div>";
    }
    ?>
</div>

</body>
</html>

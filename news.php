<?php
include("includes/db.php");
include("includes/auth.php");

if ($_SESSION['role'] !== 'orphanage') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Get orphanage_id
$stmt = $conn->prepare("SELECT orphanage_id FROM Orphanage WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$orphanage_id = $row['orphanage_id'] ?? null;

if (!$orphanage_id) {
    die("You must register an orphanage before posting news.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $event_date = $_POST['event_date'] ?? null;
    $date_posted = date("Y-m-d");
    $post_image = null;

    if (!empty($_FILES['post_image']['name'])) {
        $target_dir = "uploads/posts/";
        $filename = time() . "_" . basename($_FILES["post_image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file)) {
            $post_image = $filename;
        }
    }

    if ($title && $content) {
        $stmt = $conn->prepare("INSERT INTO Post (orphanage_id, title, content, date_posted, event_date, post_image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $orphanage_id, $title, $content, $date_posted, $event_date, $post_image);
        $stmt->execute();
        $success = "News posted successfully.";
    } else {
        $error = "All fields except image are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post News - CareConnect</title>
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
            max-width: 800px;
            margin: 40px auto;
            background: #fffbe6;
            padding: 25px;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #800000;
        }
        input[type="text"], input[type="date"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #800000;
            color: #FFD700;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .post {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .post h4 {
            color: #800000;
            margin-bottom: 5px;
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
    <h2>Post News</h2>

    <?php if (isset($success)): ?>
        <div class="message" style="color:green;"><?php echo $success; ?></div>
    <?php elseif (isset($error)): ?>
        <div class="message" style="color:red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="News Title" required>
        <textarea name="content" rows="6" placeholder="Write your update here..." required></textarea>
        <input type="date" name="event_date" required>
        <input type="file" name="post_image" accept="image/*">
        <button type="submit">Post News</button>
    </form>

    <hr>

    <h3 style="text-align:center;">Your Recent Posts</h3>

    <?php
    $posts = $conn->query("SELECT * FROM Post WHERE orphanage_id = $orphanage_id ORDER BY date_posted DESC");
    while ($row = $posts->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h4>" . htmlspecialchars($row['title']) . "</h4>";
        echo "<small>Posted on: " . $row['date_posted'] . "</small><br>";
        if (!empty($row['event_date'])) {
            echo "<small>Event Date: " . $row['event_date'] . "</small><br>";
        }
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        if (!empty($row['post_image']) && file_exists("uploads/posts/{$row['post_image']}")) {
            echo "<img src='uploads/posts/{$row['post_image']}' alt='News Image'>";
        }
        echo "</div>";
    }
    ?>
</div>

</body>
</html>

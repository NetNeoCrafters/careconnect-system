<?php
include("includes/db.php");
include("includes/auth.php");

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get user name
$user_stmt = $conn->prepare("SELECT name FROM User WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_name = $user['name'] ?? 'User';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - CareConnect</title>
    <link rel="stylesheet" href="css/style.css">
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
            display: flex;
            justify-content: space-between;
        }

        .navbar a {
            color: #FFD700;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 1080px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2, h3 {
            text-align: center;
            color: #800000;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            margin-top: 30px;
        }

        .card {
            width: 240px;
            background: #fffbe6;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #800000;
            margin-bottom: 12px;
        }

        .card .btn {
            display: block;
            background-color: #800000;
            color: #FFD700;
            padding: 8px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 8px;
        }

        .center-box {
            text-align: center;
            margin-top: 40px;
        }

        .profile-pic-large {
            width: 240px;
            height: 240px;
            border-radius: 50%;
            border: 4px solid #800000;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>CareConnect Dashboard</strong></div>
    <div>
        <a href="appointment.php">Appointments</a>
        <?php if ($role === 'donor'): ?>
            <a href="public_posts.php">News</a>
            <a href="my_events.php">My Events</a>
        <?php elseif ($role === 'orphanage'): ?>
            <a href="news.php">Post News</a>
            <a href="event_requests.php">Join Requests</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>

    <?php if ($role === 'donor'): ?>
        <h3>Verified Orphanages</h3>
        <div class="cards">
            <?php
            $result = $conn->query("SELECT * FROM Orphanage WHERE status = 'verified'");
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                if (!empty($row['profile_pic']) && file_exists("uploads/{$row['profile_pic']}")) {
                    echo "<img src='uploads/{$row['profile_pic']}' alt='Orphanage'>";
                } else {
                    echo "<div style='width:80px; height:80px; border-radius:50%; background:#ccc; margin:auto;'></div>";
                }
                echo "<h4>{$row['name']}</h4>";
                echo "<p>{$row['contact']}</p>";
                echo "<a class='btn' href='view_map.php?id={$row['orphanage_id']}'>View Location</a>";
                echo "<a class='btn' href='appointment.php?oid={$row['orphanage_id']}'>Make Appointment</a>";
                echo "</div>";
            }
            ?>
        </div>

    <?php elseif ($role === 'orphanage'): ?>
        <?php
        $stmt = $conn->prepare("SELECT * FROM Orphanage WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($orphanage = $result->fetch_assoc()) {
            echo "<div class='center-box'>";
            if (!empty($orphanage['profile_pic']) && file_exists("uploads/{$orphanage['profile_pic']}")) {
                echo "<img class='profile-pic-large' src='uploads/{$orphanage['profile_pic']}' alt='Profile Picture'><br><br>";
            } else {
                echo "<p><i>No profile picture uploaded.</i></p>";
            }
            echo "<h2>{$orphanage['name']}</h2>";
            echo "<p><strong>Contact:</strong> {$orphanage['contact']}</p>";
            echo "<p><strong>Status:</strong> <span style='color:" .
                 ($orphanage['status'] === 'verified' ? 'green' : 'orange') .
                 "'>{$orphanage['status']}</span></p>";
            echo "</div>";
        } else {
            echo "<p style='text-align:center;'>You have not registered an orphanage yet. <a href='orphanage_register.php'>Register now</a>.</p>";
        }
        ?>

    <?php elseif ($role === 'admin'): ?>
        <h3>Manage Orphanages</h3>
        <div class="cards">
            <?php
            $result = $conn->query("SELECT * FROM Orphanage ORDER BY status DESC, name ASC");
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                if (!empty($row['profile_pic']) && file_exists("uploads/{$row['profile_pic']}")) {
                    echo "<img src='uploads/{$row['profile_pic']}' alt='Profile'>";
                } else {
                    echo "<div style='width:80px; height:80px; border-radius:50%; background:#ccc; margin:auto;'></div>";
                }
                echo "<h4>{$row['name']}</h4>";
                echo "<p>{$row['contact']}</p>";
                echo "<p><strong>Status:</strong> <span style='color:" .
                     ($row['status'] === 'verified' ? 'green' : 'orange') .
                     "'>{$row['status']}</span></p>";
                echo "<a class='btn' href='?approve={$row['orphanage_id']}'>Approve</a>";
                echo "<a class='btn' href='?reject={$row['orphanage_id']}'>Reject</a>";
                echo "<a class='btn' href='?delete={$row['orphanage_id']}' onclick='return confirm(\"Delete orphanage permanently?\")'>Delete</a>";
                echo "</div>";
            }
            ?>
        </div>

        <?php
        if (isset($_GET['approve'])) {
            $oid = intval($_GET['approve']);
            $conn->query("UPDATE Orphanage SET status = 'verified' WHERE orphanage_id = $oid");
            echo "<p style='color:green; text-align:center;'>Orphanage approved.</p>";
        } elseif (isset($_GET['reject'])) {
            $oid = intval($_GET['reject']);
            $conn->query("DELETE FROM Orphanage WHERE orphanage_id = $oid");
            echo "<p style='color:red; text-align:center;'>Orphanage rejected and removed.</p>";
        } elseif (isset($_GET['delete'])) {
            $oid = intval($_GET['delete']);
            $conn->query("DELETE FROM Orphanage WHERE orphanage_id = $oid");
            echo "<p style='color:maroon; text-align:center;'>Orphanage deleted permanently.</p>";
        }
        ?>
    <?php else: ?>
        <p style="text-align:center;">Unknown role. Please contact admin.</p>
    <?php endif; ?>
</div>

</body>
</html>

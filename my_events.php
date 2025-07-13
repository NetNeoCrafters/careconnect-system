<?php
include("includes/db.php");
include("includes/auth.php");

if ($_SESSION['role'] !== 'donor') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT Post.title, Post.event_date, Post.date_posted,
           Orphanage.name AS orphanage_name,
           EventJoin.status, EventJoin.request_date
    FROM EventJoin
    JOIN Post ON EventJoin.post_id = Post.post_id
    JOIN Orphanage ON Post.orphanage_id = Orphanage.orphanage_id
    WHERE EventJoin.user_id = $user_id
    ORDER BY EventJoin.request_date DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Joined Events - CareConnect</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdfaf5;
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
            max-width: 900px;
            margin: 40px auto;
            background: #fffbe6;
            padding: 25px;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #800000;
            color: white;
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
    <h2>Events You’ve Requested to Join</h2>

    <table>
        <tr>
            <th>Event Title</th>
            <th>Orphanage</th>
            <th>Event Date</th>
            <th>Status</th>
            <th>Request Date</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['orphanage_name']) ?></td>
                <td><?= $row['event_date'] ?? 'Not set' ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td><?= $row['request_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

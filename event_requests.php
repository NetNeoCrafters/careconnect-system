<?php
include("includes/db.php");
include("includes/auth.php");

if ($_SESSION['role'] !== 'orphanage') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Get this orphanage's posts
$stmt = $conn->prepare("SELECT orphanage_id FROM Orphanage WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$orphanage_id = $row['orphanage_id'] ?? 0;

// Handle approval actions
if (isset($_GET['approve'])) {
    $eid = intval($_GET['approve']);
    $conn->query("UPDATE EventJoin SET status = 'approved' WHERE id = $eid");
}
if (isset($_GET['decline'])) {
    $eid = intval($_GET['decline']);
    $conn->query("UPDATE EventJoin SET status = 'declined' WHERE id = $eid");
}

// Fetch join requests on this orphanage's posts
$query = "
    SELECT EventJoin.id, EventJoin.status, EventJoin.request_date,
           User.name AS donor_name, Post.title AS post_title
    FROM EventJoin
    JOIN User ON EventJoin.user_id = User.user_id
    JOIN Post ON EventJoin.post_id = Post.post_id
    WHERE Post.orphanage_id = $orphanage_id
    ORDER BY EventJoin.request_date DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Join Requests</title>
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
        a.action {
            margin: 0 5px;
            padding: 4px 8px;
            background: #800000;
            color: #FFD700;
            text-decoration: none;
            border-radius: 4px;
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
    <h2>Join Requests to Your Events</h2>

    <table>
        <tr>
            <th>Donor Name</th>
            <th>Event Title</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['donor_name']) ?></td>
                <td><?= htmlspecialchars($row['post_title']) ?></td>
                <td><?= $row['request_date'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <a class="action" href="?approve=<?= $row['id'] ?>">Approve</a>
                        <a class="action" href="?decline=<?= $row['id'] ?>">Decline</a>
                    <?php else: ?>
                        <em>No action</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

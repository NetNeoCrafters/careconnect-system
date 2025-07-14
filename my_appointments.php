<?php
include("includes/db.php");
include("includes/auth.php");

if ($_SESSION['role'] !== 'donor') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT a.date, a.status, o.name AS orphanage_name
                        FROM Appointment a
                        JOIN Orphanage o ON a.orphanage_id = o.orphanage_id
                        WHERE a.user_id = ?
                        ORDER BY a.date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments - CareConnect</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fdfaf5;
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
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 750px;
            margin: 40px auto;
            background: #fffbe6;
            padding: 25px;
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            color: #800000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #ffd;
            color: #800000;
        }

        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>CareConnect - My Appointments</strong></div>
    <div><a href="dashboard.php">Dashboard</a></div>
</div>

<div class="container">
    <h2>Your Appointment History</h2>

    <?php if ($result->num_rows === 0): ?>
        <p>You haven't made any appointments yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Orphanage</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['orphanage_name']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td class="status-<?= $row['status'] ?>">
                        <?= ucfirst($row['status']) ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

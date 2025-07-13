<?php
include("includes/db.php");
include("includes/auth.php");

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments - CareConnect</title>
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
        h2 {
            text-align: center;
            color: #800000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #800000;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .status.pending { color: orange; }
        .status.approved { color: green; }
        .status.rejected { color: red; }
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
    <h2>Appointments</h2>

    <table>
        <tr>
            <th>Donor</th>
            <th>Orphanage</th>
            <th>Date</th>
            <th>Status</th>
        </tr>

        <?php
        if ($role === 'admin') {
            $query = "
                SELECT a.date, a.status, u.name AS donor_name, o.name AS orphanage_name
                FROM Appointment a
                JOIN User u ON a.user_id = u.user_id
                JOIN Orphanage o ON a.orphanage_id = o.orphanage_id
                ORDER BY a.date DESC";
        } elseif ($role === 'donor') {
            $query = "
                SELECT a.date, a.status, u.name AS donor_name, o.name AS orphanage_name
                FROM Appointment a
                JOIN User u ON a.user_id = u.user_id
                JOIN Orphanage o ON a.orphanage_id = o.orphanage_id
                WHERE a.user_id = $user_id
                ORDER BY a.date DESC";
        } elseif ($role === 'orphanage') {
            // Get orphanage ID
            $getO = $conn->prepare("SELECT orphanage_id FROM Orphanage WHERE user_id = ?");
            $getO->bind_param("i", $user_id);
            $getO->execute();
            $resO = $getO->get_result();
            $orphanage = $resO->fetch_assoc();
            $oid = $orphanage['orphanage_id'];

            $query = "
                SELECT a.date, a.status, u.name AS donor_name, o.name AS orphanage_name
                FROM Appointment a
                JOIN User u ON a.user_id = u.user_id
                JOIN Orphanage o ON a.orphanage_id = o.orphanage_id
                WHERE a.orphanage_id = $oid
                ORDER BY a.date DESC";
        } else {
            echo "<tr><td colspan='4'>Unauthorized access.</td></tr>";
            exit;
        }

        $appointments = $conn->query($query);

        if ($appointments->num_rows > 0) {
            while ($row = $appointments->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['donor_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['orphanage_name']) . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td class='status {$row['status']}'>" . ucfirst($row['status']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No appointments found.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

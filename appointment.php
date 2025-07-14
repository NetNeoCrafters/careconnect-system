<?php
include("includes/db.php");
include("includes/auth.php");

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$success = $error = "";

// —————————————————————
// DONOR ROLE HANDLING
// —————————————————————
if ($role === 'donor') {
    // ✅ Auto-redirect donor to personal appointments if no orphanage selected
    if (!isset($_GET['oid']) || !is_numeric($_GET['oid'])) {
        header("Location: my_appointments.php");
        exit;
    }

    $orphanage_id = intval($_GET['oid']);

    $stmt = $conn->prepare("SELECT name, latitude, longitude FROM Orphanage WHERE orphanage_id = ? AND status = 'verified'");
    $stmt->bind_param("i", $orphanage_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        die("Invalid or unverified orphanage.");
    }

    $orphanage = $res->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['date'];

        $check = $conn->prepare("SELECT * FROM Appointment WHERE user_id = ? AND orphanage_id = ?");
        $check->bind_param("ii", $user_id, $orphanage_id);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows > 0) {
            $error = "You already requested an appointment.";
        } else {
            $ins = $conn->prepare("INSERT INTO Appointment (user_id, orphanage_id, date, status) VALUES (?, ?, ?, 'pending')");
            $ins->bind_param("iis", $user_id, $orphanage_id, $date);
            $ins->execute();
            $success = "Appointment requested successfully.";
        }
    }
}

// —————————————————————
// ORPHANAGE ROLE HANDLING
// —————————————————————
elseif ($role === 'orphanage') {
    $stmt = $conn->prepare("SELECT orphanage_id FROM Orphanage WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orphanage = $result->fetch_assoc();
    $oid = $orphanage['orphanage_id'] ?? 0;

    if (isset($_GET['approve'])) {
        $aid = intval($_GET['approve']);
        $conn->query("UPDATE Appointment SET status='approved' WHERE appointment_id=$aid AND orphanage_id=$oid");
    } elseif (isset($_GET['reject'])) {
        $aid = intval($_GET['reject']);
        $conn->query("UPDATE Appointment SET status='rejected' WHERE appointment_id=$aid AND orphanage_id=$oid");
    }

    $appt = $conn->prepare("SELECT a.*, u.name, u.email FROM Appointment a JOIN User u ON a.user_id = u.user_id WHERE orphanage_id = ? ORDER BY date DESC");
    $appt->bind_param("i", $oid);
    $appt->execute();
    $appointments = $appt->get_result();
}

// —————————————————————
// ADMIN ROLE HANDLING
// —————————————————————
elseif ($role === 'admin') {
    $appointments = $conn->query("SELECT a.*, u.name AS donor, o.name AS orphanage
                                  FROM Appointment a
                                  JOIN User u ON a.user_id = u.user_id
                                  JOIN Orphanage o ON a.orphanage_id = o.orphanage_id
                                  ORDER BY a.date DESC");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments - CareConnect</title>
    <style>
        body { font-family: 'Segoe UI'; background: #fdfaf5; margin: 0; }
        .navbar { background: #800000; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .navbar a { color: #FFD700; font-weight: bold; text-decoration: none; }
        .container { max-width: 850px; margin: 30px auto; background: #fffbe6; padding: 25px; border-radius: 12px; }

        label { display: block; font-weight: bold; margin-top: 12px; }
        input[type="date"] { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; }
        button { margin-top: 20px; background: #800000; color: #FFD700; border: none; padding: 12px; width: 100%; border-radius: 6px; }

        .message { text-align: center; font-weight: bold; margin-top: 10px; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #ffd; color: #800000; }
        .btn { background: #800000; color: #FFD700; padding: 6px 10px; text-decoration: none; border-radius: 6px; font-size: 14px; margin-right: 6px; }
        #map { height: 300px; margin-top: 20px; border: 2px solid #800000; border-radius: 10px; }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>CareConnect - Appointments</strong></div>
    <div><a href="dashboard.php">Dashboard</a></div>
</div>

<div class="container">
<?php if ($role === 'donor'): ?>
    <h2 style="text-align:center;color:#800000;">Make Appointment with <?= htmlspecialchars($orphanage['name']) ?></h2>
    <?php if ($success): ?><div class="message success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?= $error ?></div><?php endif; ?>

    <form method="POST">
        <label>Choose Date:</label>
        <input type="date" name="date" required>
        <button type="submit">Request Appointment</button>
    </form>

    <div id="map"></div>

    <script>
        function initMap() {
            const loc = { lat: <?= $orphanage['latitude'] ?>, lng: <?= $orphanage['longitude'] ?> };
            const map = new google.maps.Map(document.getElementById("map"), {
                center: loc,
                zoom: 14,
                mapTypeId: 'hybrid'
            });
            new google.maps.Marker({ position: loc, map: map, title: "<?= htmlspecialchars($orphanage['name']) ?>" });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMqoBl0pvkWpZwPg5qqQX-9R8iHdO5oag&callback=initMap" async defer></script>

<?php elseif ($role === 'orphanage'): ?>
    <h2 style="text-align:center;color:#800000;">Appointment Requests</h2>
    <?php if ($appointments->num_rows === 0): ?><p>No appointments found.</p>
    <?php else: ?>
    <table>
        <tr><th>Donor Name</th><th>Email</th><th>Date</th><th>Status</th><th>Action</th></tr>
        <?php while ($a = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= htmlspecialchars($a['date']) ?></td>
            <td><?= ucfirst($a['status']) ?></td>
            <td>
                <?php if ($a['status'] === 'pending'): ?>
                    <a class="btn" href="?approve=<?= $a['appointment_id'] ?>">Approve</a>
                    <a class="btn" href="?reject=<?= $a['appointment_id'] ?>">Reject</a>
                <?php else: ?>—<?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php endif; ?>

<?php elseif ($role === 'admin'): ?>
    <h2 style="text-align:center;color:#800000;">All Appointments</h2>
    <?php if ($appointments->num_rows === 0): ?><p>No appointments found.</p>
    <?php else: ?>
    <table>
        <tr><th>Donor</th><th>Orphanage</th><th>Date</th><th>Status</th></tr>
        <?php while ($row = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['donor']) ?></td>
            <td><?= htmlspecialchars($row['orphanage']) ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php endif; ?>
<?php endif; ?>
</div>

</body>
</html>

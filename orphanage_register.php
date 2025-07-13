<?php
include("includes/db.php");
include("includes/auth.php");

$error = $success = "";

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if ($role !== 'orphanage') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $contact  = trim($_POST['contact']);
    $lat      = $_POST['latitude'];
    $lng      = $_POST['longitude'];

    // File upload
    $picName = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES['profile_pic']['tmp_name'];
        $ext  = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $picName = "orphanage_" . time() . "." . $ext;
        move_uploaded_file($tmp, "uploads/$picName");
    }

    if (empty($lat) || empty($lng)) {
        $error = "Please set location using the map or lat/lng.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Orphanage (user_id, name, contact, latitude, longitude, profile_pic, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("issdds", $user_id, $name, $contact, $lat, $lng, $picName);
        if ($stmt->execute()) {
            $success = "Orphanage registered successfully. Awaiting admin approval.";
        } else {
            $error = "Error: Could not register orphanage.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Orphanage - CareConnect</title>
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
            max-width: 600px;
            margin: 50px auto;
            background: #fffbe6;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #800000;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            background-color: #800000;
            color: #FFD700;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
        }

        .map-container {
            height: 300px;
            margin-top: 15px;
            border: 2px solid #800000;
            border-radius: 8px;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
        }

        .message.error { color: red; }
        .message.success { color: green; }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>CareConnect Orphanage Registration</strong></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Register Your Orphanage</h2>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
        <label>Orphanage Name:</label>
        <input type="text" name="name" required>

        <label>Contact Info:</label>
        <input type="text" name="contact" required>

        <label>Profile Picture:</label>
        <input type="file" name="profile_pic" accept="image/*">

        <label>Latitude (optional):</label>
        <input type="text" id="latitude" name="latitude" placeholder="e.g. -6.7924">

        <label>Longitude (optional):</label>
        <input type="text" id="longitude" name="longitude" placeholder="e.g. 39.2083">

        <label>Select Location on Map:</label>
        <div id="map" class="map-container"></div>

        <button type="submit">Submit</button>
    </form>
</div>

<script>
    let map, marker;

    function initMap() {
        const defaultLoc = { lat: -6.7924, lng: 39.2083 };

        const latInput = document.getElementById("latitude");
        const lngInput = document.getElementById("longitude");

        let lat = parseFloat(latInput.value);
        let lng = parseFloat(lngInput.value);

        const start = (!isNaN(lat) && !isNaN(lng)) ? { lat: lat, lng: lng } : defaultLoc;

        map = new google.maps.Map(document.getElementById("map"), {
            center: start,
            zoom: 12
        });

        marker = new google.maps.Marker({
            position: start,
            map: map,
            draggable: true
        });

        map.addListener("click", function (e) {
            marker.setPosition(e.latLng);
            latInput.value = e.latLng.lat().toFixed(6);
            lngInput.value = e.latLng.lng().toFixed(6);
        });

        latInput.addEventListener('blur', updateMarkerFromInputs);
        lngInput.addEventListener('blur', updateMarkerFromInputs);

        function updateMarkerFromInputs() {
            let lat = parseFloat(latInput.value);
            let lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                const newPos = { lat: lat, lng: lng };
                marker.setPosition(newPos);
                map.setCenter(newPos);
            }
        }
    }

    function validateForm() {
        const lat = document.getElementById("latitude").value.trim();
        const lng = document.getElementById("longitude").value.trim();
        if (!lat || !lng) {
            alert("Please enter latitude & longitude or select on map.");
            return false;
        }
        return true;
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMqoBl0pvkWpZwPg5qqQX-9R8iHdO5oag"
        async defer></script>

</body>
</html>

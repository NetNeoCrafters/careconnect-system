<?php
include("includes/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h3 style='text-align:center;color:red;'>Invalid request. Orphanage ID missing.</h3>");
}

$orphanage_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT name, latitude, longitude FROM Orphanage WHERE orphanage_id = ? AND status = 'verified'");
$stmt->bind_param("i", $orphanage_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("<h3 style='text-align:center;color:darkred;'>Invalid or unverified orphanage.</h3>");
}

$orphanage = $res->fetch_assoc();
$name = htmlspecialchars($orphanage['name']);
$lat = $orphanage['latitude'];
$lng = $orphanage['longitude'];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $name; ?> - Orphanage Location</title>
    <style>
        body {
            margin: 0;
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
            text-decoration: none;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            color: #800000;
            margin: 20px 0;
        }

        #map {
            height: 80vh;
            width: 90%;
            margin: auto;
            border: 3px solid #800000;
            border-radius: 12px;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            color: gray;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>CareConnect - Orphanage Map View</strong></div>
    <div><a href="dashboard.php">Back to Dashboard</a></div>
</div>

<h2>Location for: <?php echo $name; ?></h2>

<div id="map"></div>

<div class="footer">Map powered by Google Maps</div>

<script>
    function initMap() {
        const orphanageLoc = { lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?> };

        const map = new google.maps.Map(document.getElementById("map"), {
            center: orphanageLoc,
            zoom: 15,
            mapTypeId: "hybrid"  // satellite + street
        });

        new google.maps.Marker({
            position: orphanageLoc,
            map: map,
            title: "<?php echo $name; ?>"
        });
    }
</script>

<!-- ✅ Your actual Google Maps API key inserted here -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMqoBl0pvkWpZwPg5qqQX-9R8iHdO5oag&callback=initMap"
        async defer></script>

</body>
</html>

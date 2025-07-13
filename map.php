<?php include("includes/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Orphanage Map</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMqoBl0pvkWpZwPg5qqQX-9R8iHdO5oag"></script>
</head>
<body>
<div class="navbar">
    <div>CareConnect</div>
</div>
<div class="container">
    <h2>Orphanage Locations</h2>
    <div id="map" style="height: 500px;"></div>
</div>

<script>
function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -6.8, lng: 39.2},
        zoom: 6
    });

    <?php
    $result = $conn->query("SELECT name, location FROM Orphanage WHERE status='verified'");
    while ($row = $result->fetch_assoc()) {
        $coords = explode(",", $row['location']);
        echo "new google.maps.Marker({
            position: {lat: {$coords[0]}, lng: {$coords[1]}},
            map: map,
            title: '{$row['name']}'
        });";
    }
    ?>
}
window.onload = initMap;
</script>
</body>
</html>

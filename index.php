<!DOCTYPE html>
<html>
<head>
    <title>CareConnect - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hero {
            background-color: #fff8e1;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(128, 0, 0, 0.15);
            margin-top: 30px;
        }
        .hero h1 {
            color: #800000;
            font-size: 36px;
        }
        .hero p {
            font-size: 18px;
            color: #444;
        }
        .buttons {
            margin-top: 20px;
        }
        .buttons a {
            background-color: #800000;
            color: #FFD700;
            padding: 12px 24px;
            margin: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        .buttons a:hover {
            background-color: #a00000;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div>CareConnect</div>
    <div>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
        <a href="public_posts.php">News</a>
    </div>
</div>

<div class="container">
    <div class="hero">
        <h1>Welcome to CareConnect</h1>
        <p>A digital platform that connects donors and orphanages through location tracking, news sharing, and appointment scheduling.</p>
        <div class="buttons">
            <a href="register.php">Get Started</a>
            <a href="public_posts.php">View Orphanage News</a>
            <a href="map.php">Locate Orphanages</a>
        </div>
    </div>
</div>

</body>
</html>

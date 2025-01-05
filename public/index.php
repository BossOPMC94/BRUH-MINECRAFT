<?php
session_start();
require '../vps_functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

$user = $_SESSION['user'];
$db = connect_db();
$stmt = $db->prepare("SELECT * FROM users WHERE discord_id = ?");
$stmt->execute([$user['id']]);
$user_data = $stmt->fetch();

if (!$user_data) {
    $stmt = $db->prepare("INSERT INTO users (discord_id, username) VALUES (?, ?)");
    $stmt->execute([$user['id'], $user['username']]);
}

$vps_list = list_vps($user_data['id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>CrashDev VPS Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <button onclick="createVPS()">Create VPS</button>
    <h2>Your VPS Instances:</h2>
    <div id="vps-list"></div>

    <script>
        async function fetchVPSList() {
            const response = await fetch('api.php?action=list');
            const data = await response.json();
            const vpsList = document.getElementById('vps-list');
            vpsList.innerHTML = data.map(vps => `
                <div>
                    VPS: ${vps.container_name} (Port: ${vps.port})
                    <button onclick="deleteVPS(${vps.id})">Delete</button>
                </div>
            `).join('');
        }

        async function createVPS() {
            const response = await fetch('api.php?action=create');
            const data = await response.json();
            alert(data.message);
            fetchVPSList();
        }

        async function deleteVPS(vpsId) {
            const response = await fetch(`api.php?action=delete&id=${vpsId}`);
            const data = await response.json();
            alert(data.message);
            fetchVPSList();
        }

        fetchVPSList();
    </script>
</body>
</html>

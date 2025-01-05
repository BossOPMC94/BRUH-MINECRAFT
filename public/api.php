<?php
require '../config.php';
require '../vps_functions.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = $_SESSION['user'];

if ($_GET['action'] === 'create') {
    $vps = create_vps($user['id']);
    if ($vps) {
        echo json_encode(['success' => true, 'message' => 'VPS created', 'vps' => $vps]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create VPS']);
    }
} elseif ($_GET['action'] === 'delete') {
    $vps_id = intval($_GET['id']);
    if (delete_vps($vps_id)) {
        echo json_encode(['success' => true, 'message' => 'VPS deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete VPS']);
    }
} else {
    echo json_encode(['error' => 'Invalid action']);
}

<?php
session_start();
require '../config.php';

if (!isset($_GET['code'])) {
    $auth_url = "https://discord.com/api/oauth2/authorize?client_id=" . DISCORD_CLIENT_ID .
                "&redirect_uri=" . urlencode(DISCORD_REDIRECT_URI) .
                "&response_type=code&scope=identify";
    header("Location: $auth_url");
    exit;
}

$code = $_GET['code'];
$token_url = "https://discord.com/api/oauth2/token";
$data = [
    'client_id' => DISCORD_CLIENT_ID,
    'client_secret' => DISCORD_CLIENT_SECRET,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => DISCORD_REDIRECT_URI
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($token_url, false, $context);
$tokens = json_decode($response, true);

if (isset($tokens['access_token'])) {
    $user_info = json_decode(file_get_contents("https://discord.com/api/users/@me", false, stream_context_create([
        'http' => [
            'header' => "Authorization: Bearer " . $tokens['access_token']
        ]
    ])), true);

    $_SESSION['user'] = $user_info;
    header("Location: index.php");
} else {
    die("Failed to authenticate with Discord.");
}

<?php
require 'config.php';

function connect_db() {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    return new PDO($dsn, DB_USER, DB_PASS);
}

function create_vps($user_id) {
    $db = connect_db();
    $container_name = "vps_" . uniqid();
    $port = rand(2200, 2299);
    $ssh_key = bin2hex(random_bytes(8));

    $cmd = "docker run -itd --privileged --hostname crashcloud --cap-add=ALL -p $port:22 --name $container_name " . DOCKER_IMAGE;
    exec($cmd, $output, $result);

    if ($result === 0) {
        $stmt = $db->prepare("INSERT INTO vps (user_id, container_name, port, ssh_key) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $container_name, $port, $ssh_key]);
        return ['port' => $port, 'ssh_key' => $ssh_key];
    } else {
        return false;
    }
}

function delete_vps($vps_id) {
    $db = connect_db();
    $stmt = $db->prepare("SELECT container_name FROM vps WHERE id = ?");
    $stmt->execute([$vps_id]);
    $vps = $stmt->fetch();

    if ($vps) {
        $container_name = $vps['container_name'];
        exec("docker rm -f $container_name");
        $stmt = $db->prepare("DELETE FROM vps WHERE id = ?");
        $stmt->execute([$vps_id]);
        return true;
    }
    return false;
}

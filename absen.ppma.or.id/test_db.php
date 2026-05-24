<?php
require 'd:/koding/absensi_online_ppma/absen.ppma.or.id/includes/config.php';
$res = $conn->query('DESCRIBE guru');
while($row = $res->fetch_assoc()) echo $row['Field'] . ' - ' . $row['Type'] . "\n";

echo "\nUSERS:\n";
$res = $conn->query('SELECT * FROM users LIMIT 3');
while($row = $res->fetch_assoc()) print_r($row);

echo "\nGURU LIMIT 3:\n";
$res = $conn->query('SELECT * FROM guru LIMIT 3');
while($row = $res->fetch_assoc()) print_r($row);

<?php
$DB_HOST = 'sql211.infinityfree.com';
$DB_USER = 'if0_40608293';
$DB_PASS = 'gy9xGupHKhWxF';
$DB_NAME = 'if0_40608293_epiz_xxxxxx_projectdb';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
register_shutdown_function(function() use ($mysqli) {
    if ($mysqli) $mysqli->close();
});

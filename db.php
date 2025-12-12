<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'skill_swap_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
register_shutdown_function(function() use ($mysqli) {
    if ($mysqli) $mysqli->close();
});

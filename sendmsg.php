<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['user_id'])){
    exit('Unauthorized');
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if($receiver_id > 0 && $message != ''){
    $stmt = mysqli_prepare($mysqli , "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iis', $sender_id, $receiver_id, $message);
    mysqli_stmt_execute($stmt);
}
?>


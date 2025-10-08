<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['user_id'])){
    exit('Unauthorized');
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

if($receiver_id > 0){
    $query = "SELECT * FROM messages WHERE 
              (sender_id=$sender_id AND receiver_id=$receiver_id) OR
              (sender_id=$receiver_id AND receiver_id=$sender_id)
              ORDER BY created_at ASC";

    $result = mysqli_query($mysqli , $query);

    while($row = mysqli_fetch_assoc($result)){
        $class = ($row['sender_id'] == $sender_id) ? 'sent' : 'received';
        echo '<p class="'.$class.'">'.htmlspecialchars($row['message']).'</p>';
    }
}
?>

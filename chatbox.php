<?php
session_start();
require_once 'db.php';


if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chatbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php include "theme.php"; ?>

    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { display: flex; height: 90vh; }
        .chat-column { flex: 3; display: flex; flex-direction: column; border-right: 1px solid #ccc; padding: 10px; }
        .users-column { flex: 1; border-left: 1px solid #ccc; padding: 10px; overflow-y: auto; }
        #chat { flex: 1; border: 1px solid #ccc; overflow-y: scroll; padding: 10px; margin-bottom: 10px; }
        #chat p { margin: 5px 0; }
        .sent { text-align: right; color: blue; }
        .received { text-align: left; color: green; }
        .user-item { padding: 5px; border-bottom: 1px solid #eee; cursor: pointer; }
        .user-item:hover { background: #f0f0f0; }
        #searchUser { width: 100%; padding: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="chat-column">
        <div class="d-flex align-items-center mb-2">
            <a href="dashboard.php" class="btn btn-secondary btn-sm me-3">Back to Dashboard</a>
            <h3 class="m-0">
                <?php 
                if($receiver_id > 0){
                    $res = mysqli_query($mysqli, "SELECT name FROM users WHERE id='$receiver_id'");
                    $row = mysqli_fetch_assoc($res);
                    echo "Chat with: " . htmlspecialchars($row['name']);
                } else {
                    echo "Select a user to chat";
                }
                ?>
            </h3>
        </div>
        <div id="chat"></div>

        <?php if($receiver_id > 0): ?>
        <form id="chatForm">
            <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
            <input type="text" name="message" id="message" placeholder="Type a message" required>
            <button type="submit">Send</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="users-column">
        <input type="text" id="searchUser" placeholder="Search users...">
        <div id="usersList">
            <?php
            $users = mysqli_query($mysqli, "SELECT id, name FROM users WHERE id != '$sender_id' ORDER BY name ASC");
            while($user = mysqli_fetch_assoc($users)){
                echo '<div class="user-item" data-id="'.$user['id'].'">'.htmlspecialchars($user['name']).'</div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    <?php if($receiver_id > 0): ?>
    setInterval(loadMessages, 1000);

    function loadMessages(){
        $.ajax({
            url: 'chats.php',
            type: 'POST',
            data: {receiver_id: <?= $receiver_id ?>},
            success: function(data){
                $('#chat').html(data);
                $('#chat').scrollTop($('#chat')[0].scrollHeight);
            }
        });
    }

    $('#chatForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: 'sendmsg.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(){
                $('#message').val('');
                loadMessages();
            }
        });
    });
    <?php endif; ?>

    $(document).on('click', '.user-item', function(){
        var uid = $(this).data('id');
        window.location.href = "chatbox.php?receiver_id=" + uid;
    });

    $('#searchUser').on('keyup', function(){
        var query = $(this).val().toLowerCase();
        $('.user-item').each(function(){
            var name = $(this).text().toLowerCase();
            $(this).toggle(name.indexOf(query) !== -1);
        });
    });
});
</script>
</body>
</html>

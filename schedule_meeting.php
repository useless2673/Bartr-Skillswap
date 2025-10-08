<?php
session_start();
require_once 'db.php';

// Include PHPMailer manually (no Composer)
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$sender_name = $_SESSION['user_name'];

$receiver_id = $_GET['receiver_id'] ?? null;
$skill = $_GET['skill'] ?? '';

if(!$receiver_id || !$skill){
    echo "Invalid request.";
    exit;
}

// Fetch receiver info
$stmt = $mysqli->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows == 0){
    echo "User not found.";
    exit;
}
$receiver = $res->fetch_assoc();

// Handle form submission
$errors = [];
$success = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    if(!$date || !$time){
        $errors[] = "Please provide both date and time.";
    } else {
        // Insert into meetings table
        $stmt = $mysqli->prepare("INSERT INTO meetings (sender_id, receiver_id, skill, date, time) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iisss", $sender_id, $receiver_id, $skill, $date, $time);
        if($stmt->execute()){
            $success = "Meeting scheduled successfully!";

            // Send email to receiver
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ks9696086698@gmail.com'; // replace with your email
                $mail->Password = 'excz qxaa tvkp vmnr';    // replace with Gmail App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'SkillSwap');
                $mail->addAddress($receiver['email'], $receiver['username']);

                $mail->isHTML(true);
                $mail->Subject = "SkillSwap Meeting Scheduled";
                $jitsi_link = "https://meet.jit.si/".urlencode($sender_name."_".$receiver['username']);
                $mail->Body = "
                    <p>Hi {$receiver['username']},</p>
                    <p>{$sender_name} scheduled a meeting with you for <strong>{$skill}</strong> on {$date} at {$time}.</p>
                    <p>Join the meeting: <a href='{$jitsi_link}' target='_blank'>{$jitsi_link}</a></p>
                    <p>Regards,<br>SkillSwap Team</p>
                ";
                $mail->send();
            } catch (Exception $e){
                $errors[] = "Meeting scheduled but email not sent: {$mail->ErrorInfo}";
            }
        } else {
            $errors[] = "Failed to schedule meeting.";
        }
        $stmt->close();
    }
}

// Fetch existing meetings by me
$stmt = $mysqli->prepare("SELECT m.*, u.username as receiver_name FROM meetings m INNER JOIN users u ON m.receiver_id=u.id WHERE sender_id=? ORDER BY date,time ASC");
$stmt->bind_param("i", $sender_id);
$stmt->execute();
$meetings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Schedule Meeting - <?= htmlspecialchars($receiver['username']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2>Schedule Meeting with <?= htmlspecialchars($receiver['username']) ?> (<?= htmlspecialchars($skill) ?>)</h2>
    <a href="match_results.php?skill=<?= urlencode($skill) ?>" class="btn btn-secondary mb-3">Back to Matches</a>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3 mb-5">
        <div class="col-md-4">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Time</label>
            <input type="time" name="time" class="form-control" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">Schedule</button>
        </div>
    </form>

   <!--h3>My Scheduled Meetings</!--h3>
    <ul-- class="list-group">
        <?php while($row = $meetings->fetch_assoc()): ?>
            <li class="list-group-item">
                <?= htmlspecialchars($row['receiver_name']) ?> (<?= htmlspecialchars($row['skill']) ?>) on <?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?>
                <a href="https://meet.jit.si/<?= urlencode($sender_name."_".$row['receiver_name']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Join</a>
            </li>
        <?php endwhile; ?>
    </ul-->

</div>
</body>
</html>

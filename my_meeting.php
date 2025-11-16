<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'];

$stmt = $mysqli->prepare("
    SELECT m.*, u.username AS receiver_name 
    FROM meetings m 
    INNER JOIN users u ON m.receiver_id = u.id 
    WHERE m.sender_id = ? 
    ORDER BY m.date, m.time ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$meetings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Scheduled Meetings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php include "theme.php"; ?>

</head>
<body class="bg-light">
<div class="container py-5">
    <h2>My Scheduled Meetings</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if($meetings->num_rows > 0): ?>
        <ul class="list-group">
            <?php while($row = $meetings->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($row['receiver_name']) ?> (<?= htmlspecialchars($row['skill']) ?>) on <?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?>
                    <a href="https://meet.jit.si/<?= urlencode($username."_".$row['receiver_name']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Join</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">No meetings scheduled yet.</div>
    <?php endif; ?>
</div>
</body>
</html>

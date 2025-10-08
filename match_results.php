<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['skill'])){
    echo "No skill selected.";
    exit;
}

$skill = $_GET['skill'];

$stmt = $mysqli->prepare("SELECT users.id, users.username, users.email FROM users 
    INNER JOIN skills_have ON users.id = skills_have.user_id
    WHERE skills_have.skill = ? AND users.id != ?");
$stmt->bind_param("si", $skill, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Match Results - <?= htmlspecialchars($skill) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2>Users who know "<?= htmlspecialchars($skill) ?>"</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if($result->num_rows > 0): ?>
        <ul class="list-group">
            <?php while($row = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($row['username']) ?> (<?= htmlspecialchars($row['email']) ?>)
                    <a href="schedule_meeting.php?receiver_id=<?= $row['id'] ?>&skill=<?= urlencode($skill) ?>" class="btn btn-sm btn-primary">Schedule Meeting</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">No users found with this skill yet.</div>
    <?php endif; ?>

</div>
</body>
</html>

<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'];
$errors = [];
$success = '';

// Handle adding new skills
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add_have_skill'])){
        $skill = trim($_POST['have_skill']);
        if($skill){
            $stmt = $mysqli->prepare("INSERT INTO skills_have (user_id, skill) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $skill);
            $stmt->execute();
            $stmt->close();
            $success = "Skill added to 'Skills I Have'";
        }
    }
    if(isset($_POST['add_want_skill'])){
        $skill = trim($_POST['want_skill']);
        if($skill){
            $stmt = $mysqli->prepare("INSERT INTO skills_want (user_id, skill) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $skill);
            $stmt->execute();
            $stmt->close();
            $success = "Skill added to 'Skills I Want to Learn'";
        }
    }
    if (isset($_POST['upload_notes'])) {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $errors[] = "Title and content are required.";
    }

    if (empty($errors)) {

$stmt = $mysqli->prepare("
    INSERT INTO notes (user_id, title, content, created_at) 
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iss", $user_id, $title, $content);
        
        $stmt->execute();
        $note_id = $stmt->insert_id;
        $stmt->close();

        if (isset($_FILES['notes'])) {

            $file = $_FILES['notes'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Upload error (code {$file['error']}).";
            } else {
                $upload_dir_rel = 'storage/notes/';
                $upload_dir = __DIR__ . '/' . $upload_dir_rel; // absolute path on server

                if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
                    $errors[] = "Failed to create upload directory: {$upload_dir}";
                } elseif (!is_writable($upload_dir)) {
                    $errors[] = "Upload directory is not writable: {$upload_dir}";
                } else {
                    if (!is_uploaded_file($file['tmp_name'])) {
                        $errors[] = "No valid uploaded file found (tmp missing).";
                    } else {
                        $original_name = basename($file['name']);
                        $file_type = $file['type'];
                        $file_size = $file['size'];
                        $new_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $original_name);
                        $target_path = $upload_dir . $new_name;

                        if (move_uploaded_file($file['tmp_name'], $target_path)) {
                            // store relative path in DB
                            $db_path = $upload_dir_rel . $new_name;
                            $stmt2 = $mysqli->prepare("
                                INSERT INTO note_files 
                                (note_id, file_name, file_path, file_type, file_size, uploaded_at)
                                VALUES (?, ?, ?, ?, ?, NOW())
                            ");
                            $stmt2->bind_param("isssi", $note_id, $original_name, $db_path, $file_type, $file_size);
                            $stmt2->execute();
                            $stmt2->close();
                        } else {
                            $errors[] = "Failed to move uploaded file to storage.";
                            error_log("move_uploaded_file failed: tmp={$file['tmp_name']} target={$target_path}");
                        }
                    }
                }
            }
        }

        if (empty($errors)) {
            $success = "Note saved successfully.";
        }
    }
}

}

// Handle delete
if(isset($_GET['delete_have'])){
    $id = intval($_GET['delete_have']);
    $stmt = $mysqli->prepare("DELETE FROM skills_have WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}
if(isset($_GET['delete_want'])){
    $id = intval($_GET['delete_want']);
    $stmt = $mysqli->prepare("DELETE FROM skills_want WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// Fetch skills
$have_skills = $mysqli->query("SELECT * FROM skills_have WHERE user_id = $user_id")->fetch_all(MYSQLI_ASSOC);
$want_skills = $mysqli->query("SELECT * FROM skills_want WHERE user_id = $user_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Skill Swap</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php include "theme.php"; ?>

</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
        
        <div>
            <a href="chatbox.php" class="btn btn-success">Open Chat</a>
            <a href="my_meeting.php" class="btn btn-primary">My Meetings</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="row mb-5">
        <div class="col-md-6">
            <h3>Skills I Have</h3>
            <ul class="list-group mb-3">
                <?php foreach($have_skills as $skill): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($skill['skill']) ?>
                        <a href="?delete_have=<?= $skill['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="post" class="d-flex gap-2">
                <input type="text" name="have_skill" class="form-control" placeholder="Add skill I have" required>
                <button type="submit" name="add_have_skill" class="btn btn-primary">Add</button>
            </form>
        </div>

        <div class="col-md-6">
            <h3>Skills I Want to Learn</h3>
            <ul class="list-group mb-3">
                <?php foreach($want_skills as $skill): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($skill['skill']) ?>
                        <div>
                            <a href="match_results.php?skill=<?= urlencode($skill['skill']) ?>" class="btn btn-sm btn-success">Match</a>
                            <a href="?delete_want=<?= $skill['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="post" class="d-flex gap-2">
                <input type="text" name="want_skill" class="form-control" placeholder="Add skill I want to learn" required>
                <button type="submit" name="add_want_skill" class="btn btn-primary">Add</button>
            </form>
        </div>
    </div>
    <div>
    <h3>Upload Notes</h3>

    <form method="post" enctype="multipart/form-data" class="d-flex flex-column gap-3">
        <div>
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Enter notes title" required>
        </div>
        <div>
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" rows="4" class="form-control" placeholder="Write your content here..." required></textarea>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <input type="file" name="notes" id="notes" class="form-control flex-grow-1">
            <button type="submit" name="upload_notes" class="btn btn-primary">Upload</button>
        </div>

    </form>
</div>

</div>
</body>
</html>


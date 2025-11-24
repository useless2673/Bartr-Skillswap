<?php
session_start();
require "db.php";

if (!isset($_GET['id'])) {
    die("Note not found.");
}

$note_id = intval($_GET['id']);

$stmt = $mysqli->prepare("
    SELECT n.title, n.content, f.file_path, f.file_name 
    FROM notes n 
    LEFT JOIN note_files f ON n.id = f.note_id
    WHERE n.id = ?
");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$result = $stmt->get_result();
$note = $result->fetch_assoc();

if (!$note) {
    die("Note not found.");
}

$file_path = $note['file_path'];
$file_name = $note['file_name'];
?>
<!DOCTYPE html>
<html>
<head>
<title><?= htmlspecialchars($note['title']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include "theme.php"; ?>

</head>
<body class="p-4">
<a href="dashboard.php" class="btn btn-secondary btn-sm me-3">Back to Dashboard</a>
<h2><?= htmlspecialchars($note['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($note['content'])) ?></p>

<hr>

<h4>Attached File</h4>

<?php if ($file_path): ?>

    <?php 
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $full_url = "http://localhost/skillswap/" . $file_path;
    ?>

    <?php if ($ext === "pdf"): ?>

        <!-- PDF Preview -->
        <iframe src="<?= $file_path ?>" width="100%" height="650px" style="border:none;"></iframe>

    <?php elseif (in_array($ext, ["jpg","jpeg","png","gif","webp"])): ?>

        <!-- Image Preview -->
        <img src="<?= $file_path ?>" class="img-fluid" style="max-height:700px; object-fit:contain;">

    <?php elseif ($ext === "txt"): ?>

        <!-- TXT Preview -->
        <iframe src="<?= $file_path ?>" width="100%" height="650px" style="border:none;"></iframe>

    <?php elseif (in_array($ext, ["doc","docx","ppt","pptx","xls","xlsx"])): ?>

        <!-- Google Docs Viewer for Office Files -->
        <iframe 
            src="https://docs.google.com/gview?url=<?= urlencode($full_url) ?>&embedded=true"
            width="100%"
            height="650px"
            style="border:none;">
        </iframe>

    <?php else: ?>

        <!-- Fallback -->
        <div class="alert alert-secondary">
            Preview not available for this file type (<?= htmlspecialchars($ext) ?>).
        </div>
        <a href="<?= $file_path ?>" download class="btn btn-primary">Download File</a>

    <?php endif; ?>

<?php else: ?>
    <p>No file attached.</p>
<?php endif; ?>

</body>
</html>

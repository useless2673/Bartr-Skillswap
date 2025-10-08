<?php
session_start();

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function clean_str($s) {
    return trim($s);
}
function sanitize_output($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

$errors = [];
$success = '';

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token. Please reload the page and try again.";
    } else {
        $name = clean_str($_POST['name'] ?? '');
        $email = clean_str($_POST['email'] ?? '');
        $username = clean_str($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($name === '' || strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
        if ($username === '' || strlen($username) < 3) $errors[] = "Username must be at least 3 characters.";
        if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
        if ($password !== $confirm) $errors[] = "Passwords do not match.";

        if (empty($errors)) {
            $name_db = $mysqli->real_escape_string($name);
            $email_db = $mysqli->real_escape_string($email);
            $username_db = $mysqli->real_escape_string($username);

            $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
            if (!$stmt) {
                $errors[] = "Database error (prepare).";
            } else {
                $stmt->bind_param('ss', $email_db, $username_db);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors[] = "Email or username already taken.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $ins = $mysqli->prepare("INSERT INTO users (name, email, username, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
                    if (!$ins) {
                        $errors[] = "Database error (prepare insert).";
                    } else {
                        $ins->bind_param('ssss', $name, $email, $username, $password_hash);
                        if ($ins->execute()) {
                            $success = "Registration successful. You can now log in.";
                            unset($_SESSION['csrf_token']);
                        } else {
                            $errors[] = "Failed to register. Try again later.";
                        }
                        $ins->close();
                    }
                }
                $stmt->close();
            }
        }
    }
}
$csrf = generate_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SkillSwap — Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h3 class="card-title mb-3">Create an account — SkillSwap</h3>
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $e): ?>
                    <li><?= sanitize_output($e) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <?php if ($success): ?>
              <div class="alert alert-success"><?= sanitize_output($success) ?></div>
            <?php endif; ?>
            <form method="post" novalidate>
              <input type="hidden" name="csrf_token" value="<?= sanitize_output($csrf) ?>">
              <div class="mb-3">
                <label class="form-label">Full name</label>
                <input type="text" name="name" class="form-control" required value="<?= isset($name) ? sanitize_output($name) : '' ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required value="<?= isset($username) ? sanitize_output($username) : '' ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= isset($email) ? sanitize_output($email) : '' ?>">
              </div>
              <div class="mb-3 row">
                <div class="col">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col">
                  <label class="form-label">Confirm</label>
                  <input type="password" name="confirm_password" class="form-control" required>
                </div>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary">Register</button>
              </div>
              <p class="text-center mt-3 mb-0">
                Already have an account? <a href="login.php">Login</a>
              </p>
            </form>
          </div>
        </div>
        <p class="text-muted text-center small mt-3">By registering you agree to our Terms.</p>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

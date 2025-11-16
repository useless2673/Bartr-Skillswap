<?php
session_start();
require_once 'db.php';

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function clean_str($s) { return trim($s); }
function sanitize_output($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8'); }

$errors = [];
$success = '';
$csrf = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $identifier = clean_str($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($identifier === '' || $password === '') {
            $errors[] = "Fill all fields.";
        } else {
            $stmt = $mysqli->prepare("SELECT id, name, email, username, password_hash FROM users WHERE email = ? OR username = ? LIMIT 1");
            if (!$stmt) {
                $errors[] = "Database error.";
            } else {
                $stmt->bind_param('ss', $identifier, $identifier);//used with mysqli prepared statements
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    if (password_verify($password, $row['password_hash'])) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['user_name'] = $row['name'];
                        unset($_SESSION['csrf_token']);
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $errors[] = "Invalid credentials.";
                    }
                } else {
                    $errors[] = "Invalid credentials.";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SkillSwap — Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h3 class="mb-3">Welcome back</h3>
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $e): ?>
                    <li><?= sanitize_output($e) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <form method="post" novalidate>
              <input type="hidden" name="csrf_token" value="<?= sanitize_output($csrf) ?>">
              <div class="mb-3">
                <label class="form-label">Email or Username</label>
                <input type="text" name="identifier" class="form-control" value="<?= isset($identifier) ? sanitize_output($identifier) : '' ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary">Login</button>
              </div>
              <p class="text-center mt-3 mb-0">
                Don't have an account? <a href="register.php">Register</a>
              </p>
            </form>
          </div>
        </div>
        <p class="text-muted text-center small mt-3">Keep your account secure.</p>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

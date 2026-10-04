<?php
require_once "config.php";
if (!empty($_SESSION["user_id"])) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    $valid = false;
    if ($user) {
        $stored = $user["password"];
        $valid = password_get_info($stored)["algo"] !== null
            ? password_verify($password, $stored)
            : hash_equals($stored, $password);
        if ($valid && password_get_info($stored)["algo"] === null) {
            $upgrade = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $upgrade->execute([password_hash($password, PASSWORD_DEFAULT), $user["id"]]);
        }
    }
    if ($valid) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        header("Location: dashboard.php"); exit;
    } else $error = "Invalid username or password. Please try again.";
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | Student Feedback System</title><link rel="stylesheet" href="style.css"><script src="script.js" defer></script></head>
<body class="auth-body"><div class="auth-shell"><section class="auth-brand"><div class="cap-icon">◆</div><h1>Student Feedback &amp;<br>Faculty Evaluation System</h1><p>Build a better learning environment through honest feedback and continuous improvement.</p><ul><li>◉ &nbsp; Anonymous Feedback</li><li>◉ &nbsp; Faculty Evaluation</li><li>◉ &nbsp; Performance Reports</li><li>◉ &nbsp; Role Based Access</li></ul><small>© 2026 Student Feedback System</small></section>
<section class="auth-panel"><div class="auth-card"><h2>Welcome Back</h2><p class="muted center">Login to your account</p><?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?><?php show_flash(); ?><form method="post" class="form-stack"><label>Username<input name="username" placeholder="Enter username" autocomplete="username" required></label><label>Password<div class="password-wrap"><input id="loginPassword" type="password" name="password" placeholder="Enter password" autocomplete="current-password" required><button type="button" class="eye-btn" data-toggle-password="loginPassword">◉</button></div></label><button class="btn primary full" type="submit">Login</button></form><p class="center small">Don't have an account? <a href="signup.php">Create an account</a></p><div class="demo-box"><b>Demo Accounts</b><p>Admin: <strong>admin</strong> / Admin@123</p><p>Student: <strong>student01</strong> / Student@123</p><p>Faculty: <strong>faculty01</strong> / Faculty@123</p></div></div></section></div></body></html>

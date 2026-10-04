<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$host = "localhost";
$dbname = "student_feedback_system";
$dbuser = "root";
$dbpass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Database connection failed. Check that XAMPP MySQL is running and database.sql has been imported.");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
function require_login() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}
function require_role($role) {
    require_login();
    if (($_SESSION["role"] ?? "") !== $role) {
        header("Location: dashboard.php");
        exit;
    }
}
function flash($message, $type = "success") {
    $_SESSION["flash"] = ["message" => $message, "type" => $type];
}
function show_flash() {
    if (!empty($_SESSION["flash"])) {
        $f = $_SESSION["flash"];
        echo '<div class="alert '.e($f["type"]).'">'.e($f["message"]).'</div>';
        unset($_SESSION["flash"]);
    }
}
function app_header($title = "Dashboard") {
    $role = $_SESSION["role"] ?? "";
    $name = $_SESSION["name"] ?? "User";
    $links = [
        "dashboard.php" => ["Dashboard", "⌂"],
        "feedback.php" => ["Give Feedback", "✎"],
        "reports.php" => ["Reports", "▤"],
        "profile.php" => ["Profile", "♙"]
    ];
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | Feedback System</title><link rel="stylesheet" href="style.css"><script src="script.js" defer></script></head><body class="app-body">';
    echo '<aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">◆</span><span>Feedback<br>System</span></a><nav>';
    foreach ($links as $url => $item) {
        if ($url === "feedback.php" && $role !== "student") continue;
        if ($url === "reports.php" && $role === "student") continue;
        $active = basename($_SERVER["PHP_SELF"]) === $url ? "active" : "";
        echo '<a class="'.$active.'" href="'.e($url).'"><span>'.e($item[1]).'</span>'.e($item[0]).'</a>';
    }
    echo '<a href="logout.php"><span>⇥</span>Logout</a></nav><div class="sidebar-foot">Student Feedback System<br><small>College Evaluation Portal</small></div></aside>';
    echo '<main class="main-area"><header class="topbar"><button class="menu-toggle" id="menuButton" aria-label="Open menu">☰</button><span class="topbar-title">'.e($title).'</span><div class="user-chip">● &nbsp;'.e($name).' <small>('.e(ucfirst($role)).')</small></div></header><section class="page-content">';
    show_flash();
}
function app_footer() {
    echo '</section></main></body></html>';
}

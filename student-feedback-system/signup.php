<?php
require_once "config.php";
if (!empty($_SESSION["user_id"])) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "";
    $student_code = trim($_POST["student_code"] ?? "");
    $faculty_code = trim($_POST["faculty_code"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $semester = trim($_POST["semester"] ?? "");
    $designation = trim($_POST["designation"] ?? "");
    if (!$name || !$username || !$email || !$password || !$confirm || !in_array($role, ["student","faculty","admin"], true)) {
        $error = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif ($role === "student" && (!$student_code || !$semester)) {
        $error = "Student ID and semester are required for students.";
    } elseif ($role === "faculty" && !$faculty_code) {
        $error = "Faculty ID is required for faculty.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $error = "Username or email is already registered.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username,password,role,name,email,student_code,faculty_code,department,semester,designation,status) VALUES (?,?,?,?,?,?,?,?,?,?, 'active')");
                $stmt->execute([$username,password_hash($password,PASSWORD_DEFAULT),$role,$name,$email,$role==="student"?$student_code:null,$role==="faculty"?$faculty_code:null,$department,$role==="student"?$semester:null,$designation]);
                flash("Registration successful. Please log in.");
                header("Location: login.php"); exit;
            } catch (PDOException $e) { $error = "Could not create account. Please check the details and try again."; }
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign Up | Student Feedback System</title><link rel="stylesheet" href="style.css"><script src="script.js" defer></script></head><body class="auth-body"><div class="auth-shell"><section class="auth-brand"><div class="cap-icon">◆</div><h1>Student Feedback &amp;<br>Faculty Evaluation System</h1><p>Join our system and help improve teaching and learning through meaningful feedback.</p><ul><li>◉ &nbsp; Anonymous Feedback</li><li>◉ &nbsp; Faculty Evaluation</li><li>◉ &nbsp; Performance Reports</li><li>◉ &nbsp; Role Based Access</li></ul><small>© 2026 Student Feedback System</small></section><section class="auth-panel"><div class="auth-card signup-card"><h2>Create Your Account</h2><p class="muted">Join our system and get started</p><?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?><form method="post" id="signupForm" class="form-stack"><label>Select Role<select name="role" id="roleSelect" required><option value="student">Student</option><option value="faculty">Faculty</option><option value="admin">Admin</option></select></label><label>Full Name<input name="name" placeholder="Enter your full name" required></label><label>Username<input name="username" placeholder="Choose a username" required></label><label>Email<input type="email" name="email" placeholder="Enter your email" required></label><div id="studentFields"><label>Student ID<input name="student_code" placeholder="Enter student ID"></label><label>Semester<input name="semester" placeholder="e.g. MCA 3rd Semester"></label></div><div id="facultyFields" hidden><label>Faculty ID<input name="faculty_code" placeholder="Enter faculty ID"></label><label>Designation<input name="designation" placeholder="e.g. Assistant Professor"></label></div><div id="adminFields" hidden><label>Designation (optional)<input name="designation_admin" placeholder="e.g. Administrator"></label></div><label>Department<input name="department" placeholder="Enter department"></label><div class="two-col"><label>Password<div class="password-wrap"><input id="signupPassword" type="password" name="password" placeholder="At least 8 characters" required><button type="button" class="eye-btn" data-toggle-password="signupPassword">◉</button></div></label><label>Confirm Password<div class="password-wrap"><input id="confirmPassword" type="password" name="confirm_password" placeholder="Confirm password" required><button type="button" class="eye-btn" data-toggle-password="confirmPassword">◉</button></div></label></div><button class="btn primary full" type="submit">Sign Up</button></form><p class="center small">Already have an account? <a href="login.php">Login</a></p></div></section></div></body></html>

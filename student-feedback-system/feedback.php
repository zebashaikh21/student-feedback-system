<?php
require_once "config.php"; require_role("student");
$uid=(int)$_SESSION["user_id"]; $error="";
$faculty=$pdo->query("SELECT id,name FROM users WHERE role='faculty' AND status='active' ORDER BY name")->fetchAll();
$subjects=$pdo->query("SELECT id,subject_name FROM subjects WHERE status='active' ORDER BY subject_name")->fetchAll();
$questions=$pdo->query("SELECT id,question_text FROM questions WHERE status='active' ORDER BY id")->fetchAll();
$selectedFaculty=(int)($_POST["faculty_id"]??$_GET["faculty"]??0);
$selectedSubject=(int)($_POST["subject_id"]??$_GET["subject"]??0);
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $ratings=$_POST["rating"]??[]; $comment=trim($_POST["comment"]??"");
    if(!$selectedFaculty||!$selectedSubject||count($ratings)!==count($questions)) $error="Please select faculty, subject and answer every question.";
    else {
        foreach($questions as $q) if(!isset($ratings[$q["id"]])||!ctype_digit((string)$ratings[$q["id"]])||(int)$ratings[$q["id"]]<1||(int)$ratings[$q["id"]]>5) $error="Please provide a valid rating (1–5) for every question.";
    }
    $check=$pdo->prepare("SELECT id FROM feedback WHERE student_id=? AND faculty_id=? AND subject_id=?");$check->execute([$uid,$selectedFaculty,$selectedSubject]);
    if(!$error&&$check->fetch()) $error="You have already submitted feedback for this faculty and subject.";
    if(!$error){
        try{$pdo->beginTransaction();$stmt=$pdo->prepare("INSERT INTO feedback(student_id,faculty_id,subject_id,comment) VALUES(?,?,?,?)");$stmt->execute([$uid,$selectedFaculty,$selectedSubject,$comment]);$fid=$pdo->lastInsertId();$answer=$pdo->prepare("INSERT INTO feedback_answers(feedback_id,question_id,rating) VALUES(?,?,?)");foreach($questions as $q)$answer->execute([$fid,$q["id"],(int)$ratings[$q["id"]]]);$pdo->commit();flash("Feedback submitted successfully. Thank you!");header("Location: dashboard.php");exit;}catch(PDOException $e){if($pdo->inTransaction())$pdo->rollBack();$error="Feedback could not be saved. Please check whether you have already submitted it.";}
    }
}
app_header("Give Feedback"); ?>
<div class="welcome"><div><h1>Give Feedback</h1><p>Share your feedback to help improve teaching and learning.</p></div></div>
<div class="panel feedback-panel"><h2>Faculty Evaluation</h2><p class="muted">Your identity is kept private in faculty reports.</p><?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post" id="feedbackForm"><div class="two-col select-row"><label>Select Faculty<select name="faculty_id" required><option value="">Choose Faculty</option><?php foreach($faculty as $f): ?><option value="<?=$f["id"]?>" <?=$selectedFaculty===$f["id"]?"selected":""?>><?=e($f["name"])?></option><?php endforeach; ?></select></label><label>Select Subject<select name="subject_id" required><option value="">Choose Subject</option><?php foreach($subjects as $s): ?><option value="<?=$s["id"]?>" <?=$selectedSubject===$s["id"]?"selected":""?>><?=e($s["subject_name"])?></option><?php endforeach; ?></select></label></div>
<h3>Evaluation Questions</h3><p class="muted small">1 = Poor &nbsp; 2 = Fair &nbsp; 3 = Good &nbsp; 4 = Very Good &nbsp; 5 = Excellent</p>
<?php foreach($questions as $i=>$q): ?><div class="question-card"><p><b><?=$i+1?>.</b> <?=e($q["question_text"])?></p><div class="rating-group"><?php for($n=1;$n<=5;$n++): ?><label class="rating-option"><input type="radio" name="rating[<?=$q["id"]?>]" value="<?=$n?>" required <?=((int)($_POST["rating"][$q["id"]]??0)===$n)?"checked":""?>><span><?=$n?></span></label><?php endfor; ?></div></div><?php endforeach; ?>
<label>Comments / Suggestions<textarea name="comment" rows="4" maxlength="1000" placeholder="Enter your comments or suggestions..."><?=e($_POST["comment"]??"")?></textarea></label><div class="form-actions"><button class="btn primary" type="submit">Submit Feedback</button></div></form></div>
<?php app_footer(); ?>

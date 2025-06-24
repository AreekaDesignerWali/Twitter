<?php require 'db.php'; session_start();
if(!isset($_SESSION['user_id'])) exit;
$me=$_SESSION['user_id'];
if(isset($_GET['del'])){
  $tid=(int)$_GET['del'];
  $pdo->prepare("DELETE FROM tweets WHERE id=? AND user_id=?")->execute([$tid,$me]);
  echo "<script>redirect('profile.php');</script>"; exit;
}
if(isset($_GET['edit'])){
  $tid=(int)$_GET['edit'];
  $stmt=$pdo->prepare("SELECT content FROM tweets WHERE id=? AND user_id=?");
  $stmt->execute([$tid,$me]); $t=$stmt->fetch();
  if(!$t) exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare("UPDATE tweets SET content=? WHERE id=? AND user_id=?")
      ->execute([trim($_POST['content']), $_POST['tid'], $me]);
  echo "<script>redirect('profile.php');</script>"; exit;
}
?>
<!DOCTYPE html><html><head><title>Edit Tweet</title></head><body>
<div class="card"><h2>Edit Tweet</h2>
<form method="POST">
  <textarea name="content" rows="3"><?=htmlspecialchars($t['content']??'')?></textarea>
  <input type="hidden" name="tid" value="<?=htmlspecialchars($tid)?>">
  <button class="btn" type="submit">Save</button>
</form>
</div>
</body></html>

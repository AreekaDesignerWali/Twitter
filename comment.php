<?php require 'db.php'; session_start();
if(!isset($_SESSION['user_id'])) echo "<script>redirect('login.php');</script>";
$uid=$_SESSION['user_id']; $tid=(int)$_GET['tid'];
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['comment'])){
  $pdo->prepare("INSERT INTO comments (user_id,tweet_id,content) VALUES (?,?,?)")
      ->execute([$uid,$tid,trim($_POST['comment'])]);
  echo "<script>redirect('comment.php?tid=$tid');</script>"; exit;
}
$stmt=$pdo->prepare("SELECT t.content AS tweet, u.username, t.created_at FROM tweets t JOIN users u ON u.id=t.user_id WHERE t.id=?");
$stmt->execute([$tid]); $tweet=$stmt->fetch();
$cstmt=$pdo->prepare("SELECT c.*,u.username FROM comments c JOIN users u ON u.id=c.user_id WHERE c.tweet_id=? ORDER BY c.created_at");
$cstmt->execute([$tid]); $comments=$cstmt->fetchAll();
?>
<!DOCTYPE html><html><head><title>Comments</title></head><body>
<div class="card"><div><strong><?=htmlspecialchars($tweet['username'])?></strong> <?=nl2br(htmlspecialchars($tweet['tweet']))?></div></div>
<?php foreach($comments as $c): ?>
  <div class="card"><strong><?=htmlspecialchars($c['username'])?></strong> <small><?=date('j M H:i',strtotime($c['created_at']))?></small>
  <p><?=nl2br(htmlspecialchars($c['content']))?></p></div>
<?php endforeach; ?>
<div class="card">
  <form method="POST">
    <textarea name="comment" rows="2" placeholder="Add comment"></textarea>
    <button class="btn" type="submit">Comment</button>
  </form>
</div>
<button class="btn" onclick="redirect('home.php')">Back</button>
</body></html>

<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>"; exit;
}
$user_id = $_SESSION['user_id'];
// handle new tweet
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['tweet'])) {
    $stmt = $pdo->prepare("INSERT INTO tweets (user_id, content) VALUES (?, ?)");
    $stmt->execute([$user_id, trim($_POST['tweet'])]);
    echo "<script>window.location.href='index.php';</script>"; exit;
}
// fetch tweets from user + following
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.profile_pic,
      (SELECT COUNT(*) FROM likes l WHERE l.tweet_id = t.id) AS likes,
      (SELECT COUNT(*) FROM comments c WHERE c.tweet_id = t.id) AS comments
    FROM tweets t
    JOIN users u ON u.id = t.user_id
    WHERE t.user_id = :uid OR t.user_id IN (
      SELECT following_id FROM follows WHERE follower_id = :uid
    )
    ORDER BY t.created_at DESC
");
$stmt->execute(['uid'=>$user_id]);
$tweets = $stmt->fetchAll();
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Home • MyTwit</title>
<style>
body{font-family:sans-serif;background:#f5f8fa;margin:0;padding:0;}
.header{background:#1da1f2;color:#fff;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;}
.tweet-box{background:#fff;padding:15px;border-bottom:1px solid #e1e8ed;}
.tweet-box textarea{width:100%;border:1px solid #ccd6dd;border-radius:6px;padding:10px;resize:none;font-size:14px;}
.tweet-box button{background:#1da1f2;color:#fff;border:none;border-radius:20px;padding:10px 20px;font-size:14px;cursor:pointer;}
.tweet{background:#fff;padding:15px;border-bottom:1px solid #e1e8ed;display:flex;}
.tweet-img{width:48px;height:48px;border-radius:50%;margin-right:10px;}
.tweet-content{flex:1;}
.tweet-content .user{font-weight:bold;margin-right:5px;}
.tweet-content p{margin:5px 0;font-size:15px;}
.tweet-content .meta{font-size:13px;color:#657786;}
.interactions{margin-top:10px;font-size:14px;color:#1da1f2;}
.interactions span{margin-right:20px;cursor:pointer;}
</style>
</head><body>
<div class="header"><span>MyTwit</span><a href="profile.php" style="color:#fff;">Profile</a></div>
<div class="tweet-box">
  <form method="POST">
    <textarea name="tweet" rows="3" placeholder="What's happening?"></textarea>
    <button type="submit">Tweet</button>
  </form>
</div>
<?php foreach($tweets as $t): ?>
  <div class="tweet">
    <img src="<?= htmlspecialchars($t['profile_pic']?:'default.png') ?>" class="tweet-img">
    <div class="tweet-content">
      <div>
        <span class="user"><?= htmlspecialchars($t['username']) ?></span>
        <span class="meta"><?= date('j M H:i', strtotime($t['created_at'])) ?></span>
      </div>
      <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>
      <div class="interactions">
        <span>👍 <?= $t['likes'] ?></span>
        <span>💬 <?= $t['comments'] ?></span>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</body></html>

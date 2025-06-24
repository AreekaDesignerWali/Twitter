<?php require 'db.php'; session_start();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $display_name = trim($_POST['display_name']);
  $password = $_POST['password'];

  if (!$username || !$display_name || !$password) {
    $error = "Please fill in all fields.";
  } else {
    try {
      // Check if username already exists
      $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
      $check->execute([$username]);
      if ($check->fetch()) {
        $error = "Username already taken.";
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, display_name, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $display_name, $hash]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        echo "<script>window.location.href='home.php';</script>"; exit;
      }
    } catch (Exception $e) {
      $error = "Signup error: " . $e->getMessage();
      error_log($e->getMessage(), 3, 'phperrorlog.php');
    }
  }
}
?>
<!DOCTYPE html><html><head><title>Signup</title>
<style>
  body { font-family: sans-serif; background: #f5f8fa; padding: 50px; }
  .card { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 8px #ccc; }
  .btn { background: #1da1f2; color: white; padding: 8px 16px; border: none; border-radius: 20px; cursor: pointer; width: 100%; }
  input { width: 100%; padding: 10px; margin-bottom: 10px; }
</style>
<script>function redirect(url){ window.location.href=url; }</script>
</head><body>
<div class="card">
  <h2>Signup</h2>
  <form method="POST">
    <input name="username" placeholder="Username" required>
    <input name="display_name" placeholder="Display Name" required>
    <input type="password" name="password" placeholder="Password" required>
    <button class="btn" type="submit">Sign up</button>
  </form>
  <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
  <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body></html>

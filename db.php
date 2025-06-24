<?php
$host = 'localhost';
$db   = 'dbae05pzgxthhw';
$user = 'uc7ggok7oyoza';
$pass = 'gqypavorhbbc';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    exit('DB error');
}
?>

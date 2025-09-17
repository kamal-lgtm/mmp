<?php
// === CONFIG MYSQL (MAMP par défaut) ===
$DB_HOST = 'localhost';  // ou 127.0.0.1
$DB_USER = 'moviemak_ptf';
$DB_PASS = 'q8d0ZiAVG%';
$DB_NAME = 'moviemak_portfolio';

try {
  $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  die("Erreur connexion: " . $e->getMessage());
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function require_admin(){
  if (empty($_SESSION['admin_id'])){
    header('Location: login.php');
    exit;
  }
}

// CSRF helpers
function csrf_token(){
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function check_csrf(){
  if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
    http_response_code(400);
    echo 'CSRF token invalide'; exit;
  }
}
?>
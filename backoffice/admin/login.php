<?php
require_once __DIR__ . '/../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email'] ?? '');
  $pass  = (string)($_POST['password'] ?? '');

  $stmt = $pdo->prepare("SELECT id, email, password FROM admin WHERE email=? LIMIT 1");
  $stmt->execute([$email]);
  if ($u = $stmt->fetch()){
    $ok = false;
    if (strlen($u['password'])===32 && md5($pass) === $u['password']) $ok = true;
    if (password_verify($pass, $u['password'])) $ok = true;
    if ($ok){
      $_SESSION['admin_id'] = $u['id'];
      $_SESSION['admin_email'] = $u['email'];
      header('Location: dashboard.php'); exit;
    }
  }
  $error = "Identifiants incorrects.";
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Backoffice • Connexion</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <main class="container narrow">
    <h1>Connexion Admin</h1>
    <?php if($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="card form">
      <label>Email
        <input type="email" name="email" required placeholder="admin@admin.com">
      </label>
      <label>Mot de passe
        <input type="password" name="password" required placeholder="••••••••">
      </label>
      <button class="btn primary">Se connecter</button>
    </form>
  </main>
</body>
</html>

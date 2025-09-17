<?php include 'header.php';
$err='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();
  $nom = trim($_POST['nom'] ?? '');
  if (!$nom){ $err='Nom requis.'; }
  else {
    $st = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
    try {
      $st->execute([$nom]);
      header('Location: categories.php'); exit;
    } catch (PDOException $e){
      $err = 'Erreur: ' . $e->getMessage();
    }
  }
}
?>
<h2>Ajouter une catégorie</h2>
<?php if($err): ?><div class="alert danger"><?= e($err) ?></div><?php endif; ?>
<form class="card form" method="post">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"/>
  <label>Nom*
    <input type="text" name="nom" required>
  </label>
  <button class="btn primary">Enregistrer</button>
</form>
<?php include 'footer.php'; ?>

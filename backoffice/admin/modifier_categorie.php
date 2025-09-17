<?php include 'header.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cat = $pdo->prepare("SELECT * FROM categories WHERE id=?");
$cat->execute([$id]);
$row = $cat->fetch();
if (!$row){ echo "<p class='alert danger'>Catégorie introuvable.</p>"; include 'footer.php'; exit; }

$err='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();
  $nom = trim($_POST['nom'] ?? '');
  if (!$nom){ $err='Nom requis.'; }
  else {
    $st = $pdo->prepare("UPDATE categories SET nom=? WHERE id=?");
    try{
      $st->execute([$nom, $id]);
      header('Location: categories.php'); exit;
    } catch(PDOException $e){
      $err = 'Erreur: '.$e->getMessage();
    }
  }
}
?>
<h2>Modifier la catégorie</h2>
<?php if($err): ?><div class="alert danger"><?= e($err) ?></div><?php endif; ?>
<form class="card form" method="post">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"/>
  <label>Nom*
    <input type="text" name="nom" required value="<?= e($row['nom']) ?>">
  </label>
  <button class="btn primary">Enregistrer</button>
</form>
<?php include 'footer.php'; ?>

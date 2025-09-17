<?php include 'header.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id){
  // Option: empêcher suppression si projets existent
  $count = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE categorie_id=?");
  $count->execute([$id]);
  if ($count->fetchColumn() > 0){
    echo "<p class='alert danger'>Impossible de supprimer: des projets utilisent cette catégorie.</p>";
    echo '<p><a class="btn" href="categories.php">Retour</a></p>';
    include 'footer.php'; exit;
  }
  $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
}
header('Location: categories.php'); exit;

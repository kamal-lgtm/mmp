<?php include 'header.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id){
  // supprimer fichiers du dossier projet_X
  $dir = __DIR__ . '/../uploads/projet_' . $id;
  if (is_dir($dir)){
    foreach (scandir($dir) as $f){
      if ($f==='.'||$f==='..') continue;
      @unlink($dir . '/' . $f);
    }
    @rmdir($dir);
  }
  // supprimer cover si hors dossier projet_X
  $p = $pdo->prepare("SELECT cover FROM projects WHERE id=?");
  $p->execute([$id]);
  if ($row = $p->fetch()){
    if (!empty($row['cover'])){
      $fp = __DIR__ . '/../' . $row['cover'];
      if (is_file($fp)) @unlink($fp);
    }
  }
  $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
}
header('Location: dashboard.php'); exit;

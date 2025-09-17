<?php
require_once __DIR__ . '/../config.php';
require_admin();
check_csrf();
$mid = (int)($_POST['id'] ?? 0);
$pid = (int)($_POST['project_id'] ?? 0);
if ($mid && $pid){
  $q = $pdo->prepare("SELECT type, path FROM media WHERE id=? AND project_id=?");
  $q->execute([$mid, $pid]);
  if ($m = $q->fetch()){
    if ($m['type']==='image' && !empty($m['path'])){
      $full = __DIR__ . '/../' . $m['path'];
      if (is_file($full)) @unlink($full);
    }
    $pdo->prepare("DELETE FROM media WHERE id=?")->execute([$mid]);
  }
}
header('Location: modifier.php?id='.$pid); exit;

<?php include 'header.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$proj = $pdo->prepare("SELECT * FROM projects WHERE id=?");
$proj->execute([$id]);
$project = $proj->fetch();
if (!$project){ echo "<p class='alert danger'>Projet introuvable.</p>"; include 'footer.php'; exit; }

$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
$medias = $pdo->prepare("SELECT * FROM media WHERE project_id=? ORDER BY created_at ASC");
$medias->execute([$id]);
$medias = $medias->fetchAll();
$err='';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();
  $titre = trim($_POST['titre'] ?? '');
  $categorie_id = (int)($_POST['categorie_id'] ?? 0);
  $annee = trim($_POST['annee'] ?? '');
  $description = trim($_POST['description'] ?? '');

  $coverPath = $project['cover'];
  if (!empty($_FILES['cover']['name'])){
    $safe = time().'_'.preg_replace('~[^a-zA-Z0-9._-]~','', $_FILES['cover']['name']);
    $dest = __DIR__.'/../uploads/'.$safe;
    if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)){
      $coverPath = 'uploads/'.$safe;
    }
  }
  $pdo->prepare("UPDATE projects SET titre=?, categorie_id=?, annee=?, description=?, cover=? WHERE id=?")
      ->execute([$titre, $categorie_id, $annee, $description, $coverPath, $id]);

  // ajouter nouvelles images
  if (!empty($_FILES['images']['name'][0])){
    $projDir = __DIR__ . '/../uploads/projet_' . $id;
    if (!is_dir($projDir)) mkdir($projDir, 0777, true);
    foreach ($_FILES['images']['name'] as $i=>$n){
      if (!$n) continue;
      $safe = time().'_'.$i.'_'.preg_replace('~[^a-zA-Z0-9._-]~','', $n);
      $dest = $projDir . '/' . $safe;
      if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)){
        $rel = 'uploads/projet_'.$id.'/'.$safe;
        $pdo->prepare("INSERT INTO media (project_id, type, path) VALUES (?,?,?)")
            ->execute([$id, 'image', $rel]);
      }
    }
  }
  // ajouter nouvelles vidéos
  $videosTxt = trim($_POST['videos'] ?? '');
  if ($videosTxt){
    foreach (preg_split('~
||
~', $videosTxt) as $url){
      $url = trim($url);
      if ($url){
        $pdo->prepare("INSERT INTO media (project_id, type, path) VALUES (?,?,?)")
            ->execute([$id, 'video', $url]);
      }
    }
  }
  header('Location: modifier.php?id='.$id); exit;
}
?>
<h2>Modifier le projet</h2>
<form class="card form" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"/>
  <label>Titre*
    <input type="text" name="titre" required value="<?= e($project['titre']) ?>">
  </label>
  <label>Catégorie*
    <select name="categorie_id" required>
      <option value="">— choisir —</option>
      <?php foreach($cats as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= $c['id']==$project['categorie_id'] ? 'selected':'' ?>><?= e($c['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Année
    <input type="text" name="annee" value="<?= e($project['annee']) ?>">
  </label>
  <label>Description
    <textarea name="description" rows="4"><?= e($project['description']) ?></textarea>
  </label>
  <label>Cover (image) — actuelle :
    <?php if($project['cover']): ?><img src="../<?= e($project['cover']) ?>" class="thumb"><?php else: ?>Aucune<?php endif; ?>
    <input type="file" name="cover" accept="image/*">
  </label>
  <label>Ajouter des photos
    <input type="file" name="images[]" accept="image/*" multiple>
  </label>
  <label>Ajouter des vidéos YouTube (une par ligne)
    <textarea name="videos" rows="3" placeholder="https://youtu.be/xxx
https://www.youtube.com/watch?v=yyy"></textarea>
  </label>
  <button class="btn primary">Enregistrer</button>
</form>

<h3 class="mt">Médias existants</h3>
<div class="grid">
<?php foreach($medias as $m): ?>
  <div class="card">
    <div class="card-body">
      <p class="meta"><?= e($m['type']) ?></p>
      <?php if($m['type']==='image'): ?>
        <img src="../<?= e($m['path']) ?>" class="thumb" alt="">
      <?php else: ?>
        <a class="btn small" href="<?= e($m['path']) ?>" target="_blank">Voir la vidéo</a>
      <?php endif; ?>
      <form method="post" action="supprimer_media.php" onsubmit="return confirm('Supprimer ce média ?');" style="margin-top:8px">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"/>
        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
        <input type="hidden" name="project_id" value="<?= (int)$id ?>">
        <button class="btn danger small">Supprimer</button>
      </form>
    </div>
  </div>
<?php endforeach; if(!$medias): ?><p class="muted">Aucun média.</p><?php endif; ?>
</div>
<?php include 'footer.php'; ?>

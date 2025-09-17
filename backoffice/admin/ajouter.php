<?php include 'header.php';
$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
$err=''; $ok='';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();
  $titre = trim($_POST['titre'] ?? '');
  $categorie_id = (int)($_POST['categorie_id'] ?? 0);
  $annee = trim($_POST['annee'] ?? '');
  $description = trim($_POST['description'] ?? '');
  if (!$titre || !$categorie_id){ $err='Merci de remplir les champs obligatoires.'; }
  else {
    // Cover upload (optionnel)
    $coverPath = null;
    if (!empty($_FILES['cover']['name'])){
      $safe = time().'_'.preg_replace('~[^a-zA-Z0-9._-]~','', $_FILES['cover']['name']);
      $dest = __DIR__ . '/../uploads/' . $safe;
      if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
    $coverPath = 'uploads/'.$safe;
  } else {
    $err = 'Erreur lors de l\'upload de la cover.';
  
        $coverPath = 'uploads/'.$safe;
      }
    }
    $stmt = $pdo->prepare("INSERT INTO projects (titre, categorie_id, annee, description, cover) VALUES (?,?,?,?,?)");
    $stmt->execute([$titre, $categorie_id, $annee, $description, $coverPath]);
    $pid = (int)$pdo->lastInsertId();

    // créer dossier du projet
    $projDir = __DIR__ . '/../uploads/projet_' . $pid;
    if (!is_dir($projDir)) mkdir($projDir, 0777, true);

    // Photos multiples
    if (!empty($_FILES['images']['name'][0])){
      foreach ($_FILES['images']['name'] as $i=>$n){
        if (!$n) continue;
        $safe = time().'_'.$i.'_'.preg_replace('~[^a-zA-Z0-9._-]~','', $n);
        $dest = $projDir . '/' . $safe;
        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)){
          $rel = 'uploads/projet_'.$pid.'/'.$safe;
          $pdo->prepare("INSERT INTO media (project_id, type, path) VALUES (?,?,?)")
              ->execute([$pid, 'image', $rel]);
        }
      }
    }

    // Liens YouTube (un par ligne)
    $videosTxt = trim($_POST['videos'] ?? '');
    if ($videosTxt){
      foreach (preg_split('~
|
|
~', $videosTxt) as $url){
        $url = trim($url);
        if ($url){
          $pdo->prepare("INSERT INTO media (project_id, type, path) VALUES (?,?,?)")
              ->execute([$pid, 'video', $url]);
        }
      }
    }

    header('Location: dashboard.php'); exit;
  }
}
?>
<h2>Ajouter un projet</h2>
<?php if($err): ?><div class="alert danger"><?= e($err) ?></div><?php endif; ?>
<form class="card form" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"/>
  <label>Titre*
    <input type="text" name="titre" required>
  </label>
  <label>Catégorie*
    <select name="categorie_id" required>
      <option value="">— choisir —</option>
      <?php foreach($cats as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= e($c['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Année
    <input type="text" name="annee" placeholder="2025">
  </label>
  <label>Description
    <textarea name="description" rows="4"></textarea>
  </label>
  <label>Cover (image)
    <input type="file" name="cover" accept="image/*">
  </label>
  <label>Photos (plusieurs)
    <input type="file" name="images[]" accept="image/*" multiple>
  </label>
  <label>Liens YouTube (un par ligne)
    <textarea name="videos" rows="4" placeholder="https://youtu.be/xxxx
https://www.youtube.com/watch?v=yyyy"></textarea>
  </label>
  <button class="btn primary">Enregistrer</button>
</form>
<?php include 'footer.php'; ?>

<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/partials/header.php';

$id = (int)($_GET['id'] ?? 0);
$project = $pdo->prepare("SELECT p.*, c.nom AS categorie FROM projects p JOIN categories c ON p.categorie_id=c.id WHERE p.id=?");
$project->execute([$id]);
$project = $project->fetch();

if(!$project){ echo "<p>Projet introuvable</p>"; require_once __DIR__ . '/partials/footer.php'; exit; }

$medias = $pdo->prepare("SELECT * FROM media WHERE project_id=? ORDER BY id ASC");
$medias->execute([$id]);
$medias = $medias->fetchAll();
?>

<main class="container">
  <a href="index.php" class="btn-retour">⬅ Retour</a>
  <h1><?= e($project['titre']) ?></h1>
  <p><strong>Année :</strong> <?= e($project['annee']) ?> | <strong>Catégorie :</strong> <?= e($project['categorie']) ?></p>
  <p><?= e($project['description']) ?></p>

  <?php
  $hasImages = false;
  $hasVideos = false;
  foreach ($medias as $m) {
      if ($m['type'] === 'image') $hasImages = true;
      if ($m['type'] === 'video') $hasVideos = true;
  }
  ?>
  
  <?php if($hasVideos): ?>
    <h2>Vidéos</h2>
        <div class="grid-videos">
          <?php foreach($medias as $m): ?>
            <?php if($m['type']==='video'): ?>
              <div class="video-item">
                <?php 
                  if (
                    strpos($m['path'], 'youtube.com') !== false || 
                    strpos($m['path'], 'youtu.be') !== false || 
                    strpos($m['path'], 'shorts') !== false
                  ):
                    preg_match('/(youtu\.be\/|v=|shorts\/)([A-Za-z0-9_-]+)/', $m['path'], $match);
                    $ytid = $match[2] ?? '';
                ?>
                  <img src="https://img.youtube.com/vi/<?= $ytid ?>/hqdefault.jpg"
                       alt="YouTube video"
                       class="lightbox-trigger"
                       data-type="youtube"
                       data-src="<?= e($ytid) ?>">
                <?php else: ?>
                  <video preload="metadata"
                         class="lightbox-trigger"
                         data-type="video"
                         data-src="backoffice/<?= e($m['path']) ?>">
                    <source src="backoffice/<?= e($m['path']) ?>" type="video/mp4">
                  </video>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

  <?php endif; ?>

  <?php if($hasImages): ?>
    <h2>Photos</h2>
    <div class="grid-project masonry">
      <?php foreach($medias as $m): ?>
        <?php if($m['type']==='image'): ?>
          <div class="media-item">
            <img src="backoffice/<?= e($m['path']) ?>" alt="" loading="lazy"
                 class="lightbox-trigger"
                 data-type="image"
                 data-src="backoffice/<?= e($m['path']) ?>"
                 onerror="this.src='assets/img/placeholder.svg'">
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>

<!-- Lightbox container -->
<div id="lightbox" class="lightbox">
  <span class="close">&times;</span>
  <span class="prev">&#10094;</span>
  <span class="next">&#10095;</span>
  <div class="lightbox-content"></div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
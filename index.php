<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/partials/header.php';

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

// Fetch projects
$projects = $pdo->query("
    SELECT p.id, p.titre, p.annee, c.nom AS categorie, p.cover
    FROM projects p
    JOIN categories c ON p.categorie_id = c.id
    ORDER BY p.categorie_id ASC, p.annee DESC, p.id DESC
")->fetchAll();

?>
<main class="container">
  <h1>Portfolio</h1>
  <nav class="filters">
    <a href="#" data-filter="all" class="active">Tous</a>
    <?php foreach($categories as $cat): ?>
      <a href="#" data-filter="<?= e($cat['nom']) ?>"><?= e($cat['nom']) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="grid-home">
    <?php foreach($projects as $proj): ?>
      <a href="project.php?id=<?= $proj['id'] ?>" class="card" data-category="<?= e($proj['categorie']) ?>">
        <img src="backoffice/<?= e($proj['cover']) ?>" alt="<?= e($proj['titre']) ?>" loading="lazy" onerror="this.src='assets/img/placeholder.svg'">
        <h3><?= e($proj['titre']) ?></h3>
        <p><?= e($proj['annee']) ?> - <?= e($proj['categorie']) ?></p>
      </a>
    <?php endforeach; ?>
  </div>
</main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
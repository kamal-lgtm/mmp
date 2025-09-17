<?php include 'header.php';
$projects = $pdo->query("SELECT p.id, p.titre, p.annee, p.cover, c.nom AS categorie
                         FROM projects p JOIN categories c ON c.id=p.categorie_id
                         ORDER BY p.created_at DESC")->fetchAll();
$cats = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();
?>
<div class="actions">
  <a class="btn success" href="ajouter.php">+ Ajouter un projet</a>
  <a class="btn" href="ajouter_categorie.php">+ Ajouter une catégorie</a>
</div>

<h2>Projets</h2>
<div class="grid">
<?php foreach($projects as $p): ?>
  <article class="card">
    <div class="thumb-wrap">
      <?php if($p['cover']): ?><img src="../<?= e($p['cover']) ?>" alt="<?= e($p['titre']) ?>">
      <?php else: ?><div class="thumb-placeholder">Aucune cover</div><?php endif; ?>
    </div>
    <div class="card-body">
      <h3><?= e($p['titre']) ?></h3>
      <p class="meta"><?= e($p['categorie']) ?> • <?= e($p['annee']) ?></p>
      <div class="row-actions">
        <a class="btn small" href="modifier.php?id=<?= (int)$p['id'] ?>">Modifier</a>
        <a class="btn small danger" href="supprimer.php?id=<?= (int)$p['id'] ?>" onclick="return confirm('Supprimer ce projet ?')">Supprimer</a>
      </div>
    </div>
  </article>
<?php endforeach; if(!$projects): ?><p class="muted">Aucun projet</p><?php endif; ?>
</div>

<h2>Catégories</h2>
<table class="table">
  <thead><tr><th>Nom</th><th style="width:220px">Actions</th></tr></thead>
  <tbody>
    <?php foreach($cats as $c): ?>
    <tr>
      <td><?= e($c['nom']) ?></td>
      <td>
        <a class="btn small" href="modifier_categorie.php?id=<?= (int)$c['id'] ?>">Modifier</a>
        <a class="btn small danger" href="supprimer_categorie.php?id=<?= (int)$c['id'] ?>" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
      </td>
    </tr>
    <?php endforeach; if(!$cats): ?><tr><td colspan="2" class="muted">Aucune catégorie</td></tr><?php endif; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>

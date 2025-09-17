<?php include 'header.php';
$cats = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();
?>
<div class="actions">
  <a class="btn success" href="ajouter_categorie.php">+ Ajouter une catégorie</a>
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

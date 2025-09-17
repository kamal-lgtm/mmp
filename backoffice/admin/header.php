<?php
require_once __DIR__ . '/../config.php';
require_admin();
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Backoffice • Portfolio</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<header class="topbar">
  <div class="container">
    <strong>Backoffice</strong>
    <nav>
      <a href="dashboard.php">Dashboard</a>
      <a href="categories.php">Catégories</a>
      <a class="danger" href="logout.php">Déconnexion</a>
    </nav>
  </div>
</header>
<main class="container">

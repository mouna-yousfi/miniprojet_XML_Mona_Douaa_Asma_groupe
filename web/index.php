<?php
$xml = simplexml_load_file(__DIR__ . '/../club.xml');
$nbMembres    = count($xml->membres->membre);
$nbConcours   = count($xml->concours->concours);
$nbCategories = count($xml->categories->categorie);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Club Info_Tech</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>🖥️ Club <span>Info_Tech</span></h1>
  <nav>
    <a href="index.php" class="active">🏠 Accueil</a>
    <a href="concours.php">📋 Concours</a>
    <a href="inscription.php">✏️ Inscription</a>
    <a href="resultats.php">🏆 Résultats</a>
    <a href="requetes.php">🔍 Requêtes</a>
  </nav>
</header>
<main>
  <h2 class="page-title">🏠 Tableau de Bord</h2>
  <div class="stats-grid">
    <div class="stat-card">
      <div class="number"><?= $nbCategories ?></div>
      <div class="label">Catégories</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $nbMembres ?></div>
      <div class="label">Membres</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $nbConcours ?></div>
      <div class="label">Concours</div>
    </div>
  </div>
  <div class="card">
    <p style="color:#a8dadc; line-height:1.8;">
      Bienvenue sur la plateforme du
      <strong style="color:#4361ee">Club Info_Tech</strong>.<br>
      Gérez les membres, consultez les concours et visualisez les résultats.
    </p>
  </div>
</main>
</body>
</html>
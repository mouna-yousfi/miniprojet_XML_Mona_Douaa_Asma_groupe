<?php
$xml = simplexml_load_file(__DIR__ . '/../club.xml');
$concours = [];
foreach ($xml->concours->concours as $c) {
  $catRef  = (string)$c['categorieRef'];
  $libelle = '';
  foreach ($xml->categories->categorie as $cat) {
    if ((string)$cat['id'] === $catRef) { $libelle = (string)$cat['libelle']; break; }
  }
  $badge = 'badge-ia';
  if (str_contains(strtolower($libelle), 'web'))  $badge = 'badge-web';
  if (str_contains(strtolower($libelle), 'sécu')) $badge = 'badge-sec';
  $concours[] = [
    'titre' => (string)$c->titre,
    'date'  => (string)$c['date'],
    'coeff' => (string)$c['coefficient'],
    'cat'   => $libelle,
    'badge' => $badge,
    'nb'    => count($c->participants->participant),
  ];
}
usort($concours, fn($a,$b) => strcmp($a['date'], $b['date']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Concours — Club Info_Tech</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>🖥️ Club <span>Info_Tech</span></h1>
  <nav>
    <a href="index.php">🏠 Accueil</a>
    <a href="concours.php" class="active">📋 Concours</a>
    <a href="inscription.php">✏️ Inscription</a>
    <a href="resultats.php">🏆 Résultats</a>
    <a href="requetes.php">🔍 Requêtes</a>
  </nav>
</header>
<main>
  <h2 class="page-title">📋 Liste des Concours</h2>
  <table>
    <thead>
      <tr><th>#</th><th>Titre</th><th>Date</th><th>Catégorie</th><th>Coefficient</th><th>Participants</th></tr>
    </thead>
    <tbody>
    <?php foreach ($concours as $i => $c): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($c['titre']) ?></td>
        <td><?= $c['date'] ?></td>
        <td><span class="badge <?= $c['badge'] ?>"><?= htmlspecialchars($c['cat']) ?></span></td>
        <td><?= $c['coeff'] ?></td>
        <td><?= $c['nb'] ?> participant(s)</td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body>
</html>
<?php
$xml        = simplexml_load_file(__DIR__ . '/../club.xml');
$selectedId = $_GET['id'] ?? (string)$xml->concours->concours[0]['id'];
$tousLesConcours = [];
foreach ($xml->concours->concours as $c)
  $tousLesConcours[] = ['id'=>(string)$c['id'],'titre'=>(string)$c->titre];

$resultats = []; $titreChoisi = ''; $coeff = 1;
foreach ($xml->concours->concours as $c) {
  if ((string)$c['id'] === $selectedId) {
    $titreChoisi = (string)$c->titre;
    $coeff       = (float)$c['coefficient'];
    foreach ($c->participants->participant as $p) {
      $memRef = (string)$p['membreRef'];
      $score  = ((int)$p->complexite + (int)$p->tempsExecution) * $coeff;
      $nom    = $memRef;
      foreach ($xml->membres->membre as $m) {
        if ((string)$m['id'] === $memRef) { $nom = $m->prenom.' '.$m->nom; break; }
      }
      $resultats[] = ['nom'=>$nom,'complexite'=>(int)$p->complexite,'temps'=>(int)$p->tempsExecution,'score'=>round($score,2)];
    }
    break;
  }
}
usort($resultats, fn($a,$b) => $b['score'] <=> $a['score']);
$maxScore = !empty($resultats) ? $resultats[0]['score'] : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats — Club Info_Tech</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>🖥️ Club <span>Info_Tech</span></h1>
  <nav>
    <a href="index.php">🏠 Accueil</a>
    <a href="concours.php">📋 Concours</a>
    <a href="inscription.php">✏️ Inscription</a>
    <a href="resultats.php" class="active">🏆 Résultats</a>
    <a href="requetes.php">🔍 Requêtes</a>
  </nav>
</header>
<main>
  <h2 class="page-title">🏆 Résultats des Concours</h2>
  <div class="card">
    <form method="GET" action="resultats.php">
      <div class="form-group">
        <label>🏆 Choisir un concours</label>
        <select name="id" onchange="this.form.submit()">
          <?php foreach ($tousLesConcours as $tc): ?>
            <option value="<?= $tc['id'] ?>" <?= $tc['id']===$selectedId?'selected':'' ?>>
              <?= htmlspecialchars($tc['titre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>
  </div>
  <?php if (!empty($resultats)): ?>
  <h3 style="color:#a8dadc;margin-bottom:1rem;">
    📊 <?= htmlspecialchars($titreChoisi) ?> — Coefficient : <?= $coeff ?>
  </h3>
  <table>
    <thead>
      <tr><th>Rang</th><th>Participant</th><th>Complexité</th><th>Temps (ms)</th><th>Score</th><th>Barre</th></tr>
    </thead>
    <tbody>
    <?php foreach ($resultats as $i => $r):
      $pct = $maxScore > 0 ? round(($r['score']/$maxScore)*100) : 0;
    ?>
      <tr <?= $r['score']===$maxScore?'class="winner"':'' ?>>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($r['nom']) ?></td>
        <td><?= $r['complexite'] ?></td>
        <td><?= $r['temps'] ?></td>
        <td><strong><?= $r['score'] ?></strong></td>
        <td style="min-width:120px;">
          <div class="score-bar">
            <div class="score-fill" style="width:<?= $pct ?>%"></div>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</main>
</body>
</html>
<?php
$result = ''; $error = '';
$exemples = [
  'Liste des membres'   => 'for $m in db:open("club")//membre return <m>{$m/prenom/text()} {$m/nom/text()}</m>',
  'Titres des concours' => 'for $c in db:open("club")//concours[@id] return $c/titre/text()',
  'Scores CO1'          => 'for $p in db:open("club")//concours[@id="CO1"]//participant let $score:=($p/complexite+$p/tempsExecution)*1.5 return <r>{db:open("club")//membre[@id=$p/@membreRef]/nom/text()} — {$r/score/text()}</r>',
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $query = trim($_POST['query'] ?? '');
  if ($query) {
    $ch = curl_init('http://localhost:8080/rest/club?query='.urlencode($query));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, 'admin:admin');
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result   = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if (!$result || $httpCode >= 400) {
      $error = "❌ BaseX HTTP non disponible. Lancez basexhttp.bat !";
      $result = '';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Requêtes — Club Info_Tech</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>🖥️ Club <span>Info_Tech</span></h1>
  <nav>
    <a href="index.php">🏠 Accueil</a>
    <a href="concours.php">📋 Concours</a>
    <a href="inscription.php">✏️ Inscription</a>
    <a href="resultats.php">🏆 Résultats</a>
    <a href="requetes.php" class="active">🔍 Requêtes</a>
  </nav>
</header>
<main>
  <h2 class="page-title">🔍 Requêtes XQuery Libres</h2>
  <div class="card">
    <p style="color:#a8dadc;margin-bottom:1rem;">💡 Exemples rapides :</p>
    <?php foreach ($exemples as $label => $q): ?>
      <button class="btn" style="margin:0.3rem;font-size:0.85rem;padding:0.5rem 1rem;"
        onclick="document.getElementById('queryBox').value=<?= json_encode($q) ?>">
        <?= htmlspecialchars($label) ?>
      </button>
    <?php endforeach; ?>
  </div>
  <?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
  <?php endif; ?>
  <div class="card">
    <form method="POST" action="requetes.php">
      <div class="form-group">
        <label>✍️ Saisir une requête XQuery</label>
        <textarea name="query" id="queryBox" placeholder="for $m in db:open('club')//membre return $m/nom/text()"><?= htmlspecialchars($_POST['query'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn">▶️ Exécuter</button>
    </form>
  </div>
  <?php if ($result): ?>
    <h3 style="color:#06d6a0;margin-bottom:0.8rem;">✅ Résultat :</h3>
    <pre><?= htmlspecialchars($result) ?></pre>
  <?php endif; ?>
</main>
</body>
</html>
<?php
$xmlFile = __DIR__ . '/../club.xml';
$xml     = simplexml_load_file($xmlFile);
$message = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $membreRef  = $_POST['membre']         ?? '';
  $concoursId = $_POST['concours']       ?? '';
  $complexite = (int)($_POST['complexite']     ?? 0);
  $tempsExec  = (int)($_POST['tempsExecution'] ?? 1);

  $membreCat = ''; $concoursCat = '';
  foreach ($xml->membres->membre as $m) {
    if ((string)$m['id'] === $membreRef) { $membreCat = (string)$m['categorieRef']; break; }
  }
  foreach ($xml->concours->concours as $c) {
    if ((string)$c['id'] === $concoursId) { $concoursCat = (string)$c['categorieRef']; break; }
  }

  if ($complexite < 0 || $complexite > 100) {
    $message = "❌ Complexité doit être entre 0 et 100."; $msgType = 'error';
  } elseif ($tempsExec <= 0) {
    $message = "❌ Temps d'exécution doit être > 0."; $msgType = 'error';
  } elseif ($membreCat !== $concoursCat) {
    $message = "❌ Ce membre n'appartient pas à la catégorie de ce concours !"; $msgType = 'error';
  } else {
    $dom = new DOMDocument('1.0','UTF-8');
    $dom->preserveWhiteSpace = false; $dom->formatOutput = true;
    $dom->load($xmlFile);
    $xpath    = new DOMXPath($dom);
    $existing = $xpath->query("//concours[@id='$concoursId']//participant[@membreRef='$membreRef']");
    if ($existing->length > 0) {
      $message = "⚠️ Ce membre est déjà inscrit !"; $msgType = 'error';
    } else {
      $pNode = $dom->createElement('participant');
      $pNode->setAttribute('membreRef', $membreRef);
      $pNode->appendChild($dom->createElement('complexite', $complexite));
      $pNode->appendChild($dom->createElement('tempsExecution', $tempsExec));
      $xpath->query("//concours[@id='$concoursId']/participants")->item(0)->appendChild($pNode);
      $dom->save($xmlFile);
      $message = "✅ Inscription réussie !"; $msgType = 'success';
      $xml = simplexml_load_file($xmlFile);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription — Club Info_Tech</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>🖥️ Club <span>Info_Tech</span></h1>
  <nav>
    <a href="index.php">🏠 Accueil</a>
    <a href="concours.php">📋 Concours</a>
    <a href="inscription.php" class="active">✏️ Inscription</a>
    <a href="resultats.php">🏆 Résultats</a>
    <a href="requetes.php">🔍 Requêtes</a>
  </nav>
</header>
<main>
  <h2 class="page-title">✏️ Inscription à un Concours</h2>
  <?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?>"><?= $message ?></div>
  <?php endif; ?>
  <div class="card">
    <form method="POST" action="inscription.php">
      <div class="form-group">
        <label>👤 Membre</label>
        <select name="membre" required>
          <option value="">-- Choisir un membre --</option>
          <?php foreach ($xml->membres->membre as $m): ?>
            <option value="<?= $m['id'] ?>">
              <?= $m['prenom'] ?> <?= $m['nom'] ?> (<?= $m['id'] ?> — <?= $m['categorieRef'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>🏆 Concours</label>
        <select name="concours" required>
          <option value="">-- Choisir un concours --</option>
          <?php foreach ($xml->concours->concours as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c->titre ?> (<?= $c['categorieRef'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>⚙️ Complexité (0 → 100)</label>
        <input type="number" name="complexite" min="0" max="100" value="50" required>
      </div>
      <div class="form-group">
        <label>⏱️ Temps d'exécution (ms)</label>
        <input type="number" name="tempsExecution" min="1" value="100" required>
      </div>
      <button type="submit" class="btn">✅ S'inscrire</button>
    </form>
  </div>
</main>
</body>
</html>
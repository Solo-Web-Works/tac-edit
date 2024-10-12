<?php
$file = '/data/' . basename($_GET['file']);
$yamlData = file_get_contents($file);
$parsedYaml = yaml_parse($yamlData);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $confUpdated = $_POST['yamlContent'];

  // Save the updated YAML
  file_put_contents($file, $confUpdated);

  // Redirect to avoid form resubmission
  header('Location: tacCommit.php?file=' . urlencode(basename($file)));
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAC Stack Config Editor - Edit <?php echo htmlspecialchars(basename($file)); ?></title>
</head>
<body>
  <h1>TAC Stack Config Editor</h1>
  <h2>Editing <?php echo htmlspecialchars(basename($file)); ?></h2>

  <form method="POST">
    <textarea name="yamlContent" rows="20" cols="80"><?php echo htmlspecialchars($yamlData); ?></textarea>
    <br>
    <button type="submit">Save Changes</button>
  </form>
</body>
</html>

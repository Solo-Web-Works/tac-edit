<?php
// Display a simple interface for managing JSON files
$files = scandir('/data');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAC Stack Config Editor</title>
</head>
<body>
  <h1>TAC Stack Config Editor</h1>
  <ul>
    <?php foreach ($files as $file): ?>
      <?php if (pathinfo($file, PATHINFO_EXTENSION) === 'yaml' || pathinfo($file, PATHINFO_EXTENSION) === 'yml'): ?>
        <li>
          <a href="tacEdit.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</body>
</html>

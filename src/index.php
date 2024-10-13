<?php
// Display a simple interface for managing JSON files
// Change line below before building docker image
// $files = scandir('/data');
$files = scandir('../config-test');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAC Stack Config Editor</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div id="wrapper">
    <h1>TAC Stack Config Editor</h1>

    <table>
      <thead>
        <tr>
          <th>File Name</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($files as $file): ?>
          <?php if (pathinfo($file, PATHINFO_EXTENSION) === 'yaml' || pathinfo($file, PATHINFO_EXTENSION) === 'yml'): ?>
            <tr><td><?php echo htmlspecialchars($file); ?></td> <td><a href="tacEdit.php?file=<?php echo urlencode($file); ?>">Edit</a></td></tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a class="btn" href="tacAdd.php">Add a New Host</a>
  </div>
</body>
</html>

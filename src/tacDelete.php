<?php
// Remove log lines before building docker image
error_reporting(E_ALL & ~E_NOTICE);
ini_set("log_errors", TRUE);
ini_set("error_log", "../logs/error.log");

// Change line below before building docker image
//$directory = '/data/';
$directory = '../config-test/';
$hosts = array_diff(scandir($directory), ['.', '..']);  // List all files except . and ..

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteHost'])) {
  $hostToDelete = $_POST['deleteHost'];

  // Delete the YAML file
  $filePath = $directory . basename($hostToDelete);

  if (file_exists($filePath)) {
    unlink($filePath);  // Delete the file

    // Redirect after deletion to avoid form resubmission
    header('Location: tacDelete.php?deleted=1');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAC-Stack Config Editor - Delete a Host</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div id="wrapper">
    <h1>TAC Stack Config Editor</h1>
    <h2>Delete a Host</h2>

    <a href="index.php">&laquo; Back to file list</a>

    <?php if (isset($_GET['deleted'])): ?>
      <p>Host deleted successfully!</p>
    <?php endif; ?>

    <form method="POST">
      <ul>
        <?php foreach ($hosts as $host): ?>
          <?php if (pathinfo($host, PATHINFO_EXTENSION) === 'yaml' || pathinfo($host, PATHINFO_EXTENSION) === 'yml'): ?>
            <li>
              <?php echo htmlspecialchars($host); ?>
              <button class="del" type="submit" name="deleteHost" value="<?php echo htmlspecialchars($host); ?>" onclick="return confirm('Are you sure you want to delete this host?');">
                Delete
              </button>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </form>
  </div>
</body>
</html>

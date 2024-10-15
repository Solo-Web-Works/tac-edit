<?php
// Remove log lines before building docker image
error_reporting(E_ALL & ~E_NOTICE);
ini_set("log_errors", true);
ini_set("error_log", "/opt/tac-edit/logs/error.log");

include('includes/library.php');

// Change line below before building docker image
// $directory = '/data/';
// $files = scandir('/data');
$directory = '/opt/tac-edit/config-test/';
$files = scandir($directory);

// Handle deletion of a host directly in index.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteHost'])) {
  $fileToDelete = $_POST['deleteHost'];

  // Delete the YAML file
  $filePath = $directory . basename($fileToDelete);

  if (file_exists($filePath)) {
    unlink($filePath);  // Delete the file

    delHostVersions(basename($_POST['deleteHost']));  // Delete the host's versions

    // Redirect to avoid form resubmission
    header('Location: index.php?deleted=1&file='.urlencode($fileToDelete));
    exit();
  }
}

// Optionally, add a success message
if (isset($_GET['deleted'])) {
  $file = basename(htmlspecialchars($_GET['file']),'.yml');
  $msgSuccess = "Host $file deleted successfully!";
}
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

    <?php if (isset($msgSuccess)): ?>
      <p class="success p-1 mb-2"><?php echo $msgSuccess; ?></p>
    <?php endif; ?>

    <div class="fileList">
      <div class="fileHead row heading">
        <div class="cell">File Name</div>
        <div class="cell">Actions</div>
      </div>

      <form method="POST">
        <?php foreach ($files as $file): ?>
          <?php if (pathinfo($file, PATHINFO_EXTENSION) === 'yaml' || pathinfo($file, PATHINFO_EXTENSION) === 'yml'): ?>
            <div class="fileEntry row">
              <div class="cell"><?php echo htmlspecialchars($file); ?></div>
              <div class="btnGroup mr-1">
                <a class="btn sm" href="tacEdit.php?file=<?php echo urlencode($file); ?>">Edit</a>

                <button class="sm danger" type="submit" name="deleteHost" value="<?php echo htmlspecialchars($file); ?>" onclick="return confirm('Are you sure you want to delete this host?');">
                  Delete
                </button>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </form>
    </div>

    <a class="btn" href="tacAdd.php">Add New Host</a>
  </div>
</body>
</html>

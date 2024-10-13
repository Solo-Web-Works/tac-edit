<?php
// Change line below before building docker image
// $repoDir = '/data';
$repoDir = '../config-test';
$file = basename($_GET['file']);

// Set the commit message
$commitMessage = date('Y-m-d H:i:s')." - Updated ".$file;

// Run Git commands to commit the changes
exec("cd $repoDir && git add $file && git commit -m '$commitMessage'");
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

    Processed commit for <?php echo htmlspecialchars($file); ?>.<br><br>
    <a href="index.php">Back to file list</a>
  </div>
</body>
</html>

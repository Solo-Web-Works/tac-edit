<?php
$file = basename($_GET['file']);
$repoDir = '/data';
$commitMessage = "Updated " . $file;

// Run Git commands to commit the changes
exec("cd $repoDir && git add $file && git commit -m '$commitMessage'");

echo "Changes committed for $file.";
?>

<a href="index.php">Back to file list</a>

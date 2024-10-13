<?php
// Change line below before building docker image
// $repoDir = '/data';
$repoDir = '../config-test';
$file = basename($_GET['file']);
$commitMessage = "Updated " . $file;

// Run Git commands to commit the changes
exec("cd $repoDir && git add $file && git commit -m '$commitMessage'");

echo "Changes committed for $file.";
?>

<a href="index.php">Back to file list</a>

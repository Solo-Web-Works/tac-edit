<?php
// Change line below before building docker image
// $repoDir = '/data';
$repoDir = '../config-test';
$file = basename($_GET['file']);

// Set the commit message
$commitMessage = date('Y-m-d H:i:s')." - Updated ".$file;

// Run Git commands to commit the changes
exec("cd $repoDir && git add $file && git commit -m '$commitMessage'");

echo "Changes committed for $file.";
?>

<a href="tacEdit.php?file=<?php echo urlencode($file); ?>">Back to editing</a>

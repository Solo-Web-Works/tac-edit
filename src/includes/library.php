<?php
error_reporting(E_ALL & ~E_NOTICE);

function saveVersion($hostName, $yamlContent, $comment) {
  // Load the metadata file

  // Change paths below before building docker image

  $versionsFile = '/versions/versions.json';
  // $versionsFile = '/opt/tac-edit/versions/versions.json';
  $versionsData = json_decode(file_get_contents($versionsFile), true);

  // Determine the new version number
  $currentVersion = isset($versionsData[$hostName]['current_version']) ? $versionsData[$hostName]['current_version'] : 'v0';
  $newVersionNumber = intval(str_replace('v', '', $currentVersion)) + 1;
  $newVersion = 'v' . $newVersionNumber;

  // Save the new versioned YAML file
  $newFileName = '/versions/' . basename($hostName,'.yml') . "_$newVersion.yml";
  // $newFileName = '/opt/tac-edit/versions/' . basename($hostName,'.yml') . "_$newVersion.yml";
  file_put_contents($newFileName, $yamlContent);

  // Update the metadata
  $versionsData[$hostName]['current_version'] = $newVersion;
  $versionsData[$hostName]['versions'][] = [
      'version' => $newVersion,
      'date'    => date('Y-m-d H:i:s'),
      'comment' => $comment
  ];

  // Save the updated metadata back to the file
  file_put_contents($versionsFile, json_encode($versionsData, JSON_PRETTY_PRINT));
}

function getHostVersions($hostName) {
  $versionsFile = '/versions/versions.json';
  // $versionsFile = '/opt/tac-edit/versions/versions.json';
  $versionsData = json_decode(file_get_contents($versionsFile), true);

  return $versionsData[$hostName]['versions'] ?? [];
}

function delHostVersions($hostName) {
  // Load the versions.json file
  $versionsFile = '/versions/versions.json';
  // $versionsFile = '/opt/tac-edit/versions/versions.json';
  $versionsData = json_decode(file_get_contents($versionsFile), true);

  // Check if the host exists in the metadata
  if (isset($versionsData[$hostName])) {
    // Remove the host from the metadata
    unset($versionsData[$hostName]);

    // Save the updated metadata back to the versions.json file
    file_put_contents($versionsFile, json_encode($versionsData, JSON_PRETTY_PRINT));

    // Delete all versioned YAML files for the host
    // $versionedFiles = glob("/versions/{$hostName}_v*.yml");  // Find all versioned files
    $hostBase = basename($hostName, '.yml');
    $versionedFiles = glob("/versions/{$hostBase}_v*.yml");  // Find all versioned files
    // $versionedFiles = glob("/opt/tac-edit/versions/{$hostBase}_v*.yml");  // Find all versioned files

    foreach ($versionedFiles as $file) {
      if (file_exists($file)) {
        unlink($file);  // Delete each file
      }
    }

    return true;  // Successfully deleted
  }

  return false;  // Host not found
}


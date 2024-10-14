<?php
function saveVersion($hostName, $yamlContent, $comment) {
  // Load the metadata file

  // Change lines below before building docker image

  // $versionsFile = '/versions/versions.json';
  $versionsFile = '../versions/versions.json';
  $versionsData = json_decode(file_get_contents($versionsFile), true);

  // Determine the new version number
  $currentVersion = isset($versionsData[$hostName]['current_version']) ? $versionsData[$hostName]['current_version'] : 'v0';
  $newVersionNumber = intval(str_replace('v', '', $currentVersion)) + 1;
  $newVersion = 'v' . $newVersionNumber;

  // Save the new versioned YAML file
  // $newFileName = '/versions/' . $hostName . "_$newVersion.yaml";
  $newFileName = '../versions/' . $hostName . "_$newVersion.yaml";
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


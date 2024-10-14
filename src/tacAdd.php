<?php
// Remove log lines before building docker image
error_reporting(E_ALL & ~E_NOTICE);
ini_set("log_errors", true);
ini_set("error_log", "../logs/error.log");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newHost'])) {
  // Gather form data
  $newHostName = $_POST['newHostName'];
  $rule = 'Host(`' . $_POST['newHostRule'] . '`)';  // Rebuild the full rule string
  $middlewares = ['default-middlewares'];  // Always include default-middlewares

  if (isset($_POST['newHostAuthenticated'])) { $middlewares[] = 'authelia'; } // Add authelia if authenticated is checked

  // Build the new host's router and service data
  $newRouter = [
    'entryPoints' => ['https'],  // Static entryPoints set to https
    'rule' => $rule,
    'middlewares' => $middlewares,
    'service' => $newHostName  // Service is automatically set to router name
  ];

  $newService = [
    'loadBalancer' => [
      'servers' => [
        ['url' => $_POST['newHostServerUrl']]
      ],
      'passHostHeader' => isset($_POST['newHostPassHostHeader']) ? true : false
    ]
  ];

  // Save the new router and service to a new YAML file
  $newYamlData['http'] = [
    'routers' => [$newHostName => $newRouter],
    'services' => [$newHostName => $newService]
  ];

  // Change line below before building docker image
  // $newFileName = '/data/' . $newHostName . '.yml';  // Create a new file for this host
  $newFileName = '../config-test/' . $newHostName . '.yml';  // Create a new file for this host
  $newYamlContent = yaml_emit($newYamlData);
  file_put_contents($newFileName, $newYamlContent);

  // Redirect after creation to avoid form resubmission
  header('Location: tacCommit.php?file=' . urlencode($newHostName . '.yaml'));
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAC-Stack Config Editor - Add New Host</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div id="wrapper">
    <h1>TAC Stack Config Editor</h1>
    <h2>Add a New Host</h2>

    <a href="index.php">&laquo; Back to file list</a>

    <form method="POST">
      <div class="gridContainer mt-1">
        <fieldset>
          <legend>New Host</legend>

          <input type="hidden" name="newHost" value="1" />

          <!-- Router Data -->
          <h3>Router</h3>
          <label>Host Name<span class="required">*</span>:
            <input type="text" name="newHostName" required />
          </label>

          <label>Rule (Domain Only)<span class="required">*</span>:
            <input type="text" name="newHostRule" required />
          </label>

          <label>Authenticated:
            <input type="checkbox" name="newHostAuthenticated" />
          </label>

          <!-- Service Data -->
          <h3 class="mt-1">Service</h3>
          <label>Server URL<span class="required">*</span>:
            <input type="text" name="newHostServerUrl" required />
          </label>

          <label>Pass Host Header:
            <input type="checkbox" name="newHostPassHostHeader" />
          </label>
        </fieldset>
      </div>

      <button type="submit">Create New Host</button>
    </form>
  </div>
</body>
</html>

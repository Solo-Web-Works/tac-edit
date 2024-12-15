<?php
include('includes/library.php');

// Change line below before building docker image
$file = '/data/' . basename($_GET['file']);
// $file        = '../config-test/' . basename($_GET['file']);
$yamlData   = file_get_contents($file);
$parsedYaml = yaml_parse($yamlData);
$parsedYaml = $parsedYaml['http'];

// Group routers and services by name
$combinedData = [];

foreach ($parsedYaml['routers'] as $name => $router) {
  $combinedData[$name]['router'] = $router;

  if (isset($parsedYaml['services'][$name])) { $combinedData[$name]['service'] = $parsedYaml['services'][$name]; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $parsedYaml = ['---','http' => ['routers' => [], 'services' => []]];

  // Update routers and services based on submitted data
  foreach ($_POST['units'] as $name => $unit) {
    $rule        = 'Host(`' . $unit['router']['rule'] . '`)';  // Rebuild the full rule string
    $middlewares = ['default-middlewares'];                    // Always include default-middlewares

    if (isset($unit['router']['authenticated'])) { $middlewares[] = 'authelia'; }  // Add authelia if authenticated is checked

    $parsedYaml['http']['routers'][$name] = [
      'entryPoints' => ['https'],    // Static entryPoints set to https
      'rule'        => $rule,
      'middlewares' => $middlewares,
      'service'     => $name         // Service is automatically set to router name
    ];

    // Update service data
    if (isset($unit['service'])) {
      $parsedYaml['http']['services'][$name]['loadBalancer']['servers'][0]['url'] = $unit['service']['loadBalancer']['servers'][0]['url'];
      // Handle Pass Host Header as a boolean
      $parsedYaml['http']['services'][$name]['loadBalancer']['passHostHeader'] = isset($unit['service']['loadBalancer']['passHostHeader']) ? true : false;
    }
  }

  //Save the updated YAML
  array_splice($parsedYaml, 0, 1);
  $updatedYaml = yaml_emit($parsedYaml);

  file_put_contents($file, $updatedYaml);

  saveVersion(basename($file), $updatedYaml, $_POST['msgVersion']);

  $msgSuccess = 'Host '.basename($_GET['file'],'.yml').' edited successfully!';
}

// Optionally, add a success message
if (isset($_GET['added'])) {
  $msgSuccess = 'Host '.basename($_GET['file'],'.yml').' added successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAC Stack Config Editor - Edit <?php echo htmlspecialchars(basename($file)); ?></title>
  <link rel="stylesheet" href="style.css">

  <link rel="icon" href="images/icon.webp" type="image/webp">
</head>

<body>
  <div id="wrapper">
    <div class="flex gap-1">
      <img src="images/icon.webp" alt="TAC-Edit Icon" class="h-12 w-12"> <h1>TAC Stack Config Editor</h1>
    </div>

    <?php if (isset($msgSuccess)): ?>
      <p class="success p-1 mb-2"><?php echo $msgSuccess; ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="gridContainer mt-1 mb-3">
        <!-- Combined Routers and Services Section -->
        <?php foreach ($combinedData as $name => $data): ?>
          <fieldset>
            <legend>Editing Server: <?php echo htmlspecialchars($name); ?></legend>

            <!-- Router Data -->
            <h3>Router</h3>
            <label>Rule:
              <?php
              // Extract the domain part from the rule
              preg_match("/Host\\(`(.*?)`\\)/", $data['router']['rule'], $matches);
              $domain = $matches[1] ?? '';
              ?>
              <input type="text" name="units[<?php echo $name; ?>][router][rule]" value="<?php echo htmlspecialchars($domain); ?>" />
            </label>

            <!-- Middlewares - Replace with Authenticated Checkbox -->
            <label>Authenticated:
              <input type="checkbox" name="units[<?php echo $name; ?>][router][authenticated]" <?php echo in_array('authelia', $data['router']['middlewares']) ? 'checked' : ''; ?> />
            </label>

            <!-- Service Data -->
            <h3 class="mt-1">Service</h3>
            <label>Load Balancer Servers:
              <input type="text" name="units[<?php echo $name; ?>][service][loadBalancer][servers][0][url]" value="<?php echo htmlspecialchars($data['service']['loadBalancer']['servers'][0]['url']); ?>" />
            </label>

            <label>Pass Host Header:
              <input type="checkbox" name="units[<?php echo $name; ?>][service][loadBalancer][passHostHeader]" <?php echo $data['service']['loadBalancer']['passHostHeader'] ? 'checked' : ''; ?> />
            </label>

            <label for="">Version Message:
              <input type="text" name="msgVersion" />
            </label>
          </fieldset>
        <?php endforeach; ?>

        <div class="versions">
          <h2>Version History</h2>
          <?php
          $versions = getHostVersions(basename($file));

          if (count($versions) > 0) {
            echo '<ul>';
            foreach ($versions as $version) {
              echo '<li><strong>'.$version['version'].'</strong> - '.$version['date'].' - '.$version['comment'].'</li>';
            }
            echo '</ul>';
          } else {
            echo '<p>No versions found for this host.</p>';
          }
          ?>
        </div>
      </div>

      <div class="btnGroup">
        <a class="btn danger" href="index.php">Cancel Changes</a>
        <button class="success" type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</body>
</html>

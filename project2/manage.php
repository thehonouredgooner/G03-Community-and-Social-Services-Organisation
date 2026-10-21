<?php
// manage.php — HR manager page for working with Expressions of Interest.
// Owner: Lan Nguyen (Task 6, manager queries)
//
// All PHP logic runs before any HTML is sent, so header() redirects
// still work when the manager is not logged in.

session_start();

$currentPage = 'manage';   // not in the main nav, so no link is highlighted

// ----------------------------------------------------------
// Access control
// Only a logged-in HR manager may see this page. The login page
// stores the username in the session after a successful login.
// ----------------------------------------------------------
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/settings.php';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed.");
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="HR manager page for reviewing and managing Expressions of Interest.">
  <meta name="robots" content="noindex, nofollow">
  <title>Manage EOIs | Community Connect</title>

  <link rel="stylesheet" href="styles/styles.css">
</head>

<body class="page-manage">
  <a class="skip-link" href="#main-content">Skip to main content</a>

  <?php require __DIR__ . '/header.inc'; ?>
  <?php require __DIR__ . '/nav.inc'; ?>

  <main id="main-content">
    <h1>Manage Expressions of Interest</h1>
    <p>
      Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.
      Use the options below to search, sort and update applications.
    </p>
  </main>

  <!-- Shared footer -->
  <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>

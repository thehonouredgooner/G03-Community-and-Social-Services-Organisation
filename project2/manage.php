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

// ----------------------------------------------------------
// Make sure the eoi table exists before querying it.
// process_eoi.php creates it on the first application, so on a
// fresh server it may not be there yet.
// ----------------------------------------------------------
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'eoi'");
$eoiTableExists = ($tableCheck && mysqli_num_rows($tableCheck) > 0);

// ----------------------------------------------------------
// Build and run the query
// ----------------------------------------------------------
$results     = [];
$resultTitle = "All Expressions of Interest";

if ($eoiTableExists) {
    $sql = "SELECT * FROM eoi ORDER BY EOInumber ASC";
    $queryResult = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($queryResult)) {
        $results[] = $row;
    }
}

mysqli_close($conn);

// ----------------------------------------------------------
// Display helpers
// ----------------------------------------------------------

// Escapes a value before it is printed into the page.
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Turns the four skill columns into one readable list.
function format_skills($row) {
    $labels = [
        'skill_maintenance'      => 'Maintenance',
        'skill_event_planning'   => 'Event planning',
        'skill_advertising'      => 'Advertising',
        'skill_customer_service' => 'Customer service',
    ];

    $skills = [];
    foreach ($labels as $column => $label) {
        if (!empty($row[$column])) {
            $skills[] = $label;
        }
    }

    return count($skills) > 0 ? implode(', ', $skills) : 'None selected';
}
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

    <section aria-labelledby="results-heading">
      <h2 id="results-heading"><?php echo e($resultTitle); ?></h2>

      <?php if (!$eoiTableExists) { ?>
        <p class="notice">No applications have been submitted yet.</p>

      <?php } elseif (count($results) === 0) { ?>
        <p class="notice">No Expressions of Interest match your query.</p>

      <?php } else { ?>
        <p><?php echo count($results); ?> record(s) found.</p>

        <div class="table-scroll">
          <table class="eoi-table">
            <thead>
              <tr>
                <th scope="col">EOI #</th>
                <th scope="col">Job ref</th>
                <th scope="col">Applicant</th>
                <th scope="col">Date of birth</th>
                <th scope="col">Gender</th>
                <th scope="col">Address</th>
                <th scope="col">Contact</th>
                <th scope="col">Skills</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $row) { ?>
                <tr>
                  <td><?php echo e($row['EOInumber']); ?></td>
                  <td><?php echo e($row['job_reference']); ?></td>
                  <td><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                  <td><?php echo e($row['date_of_birth']); ?></td>
                  <td><?php echo e($row['gender']); ?></td>
                  <td>
                    <?php echo e($row['street_address']); ?><br>
                    <?php echo e($row['suburb'] . ' ' . strtoupper($row['state']) . ' ' . $row['postcode']); ?>
                  </td>
                  <td>
                    <?php echo e($row['email']); ?><br>
                    <?php echo e($row['phone']); ?>
                  </td>
                  <td>
                    <?php echo e(format_skills($row)); ?>
                    <?php if (!empty($row['other_skills'])) { ?>
                      <br><em>Other:</em> <?php echo e($row['other_skills']); ?>
                    <?php } ?>
                  </td>
                  <td><?php echo e($row['status']); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } ?>
    </section>
  </main>

  <!-- Shared footer -->
  <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>

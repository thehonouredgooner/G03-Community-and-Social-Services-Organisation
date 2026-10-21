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

// Cleans one value that came from the browser.
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ----------------------------------------------------------
// Read which query the manager asked for (sent with GET so the
// results page can be bookmarked or refreshed safely).
// ----------------------------------------------------------
$query  = isset($_GET['query']) ? clean_input($_GET['query']) : 'all';
$jobRef = isset($_GET['job_ref']) ? strtoupper(clean_input($_GET['job_ref'])) : '';
// Names are decoded again after cleaning so an apostrophe (O'Brien) can be
// validated and matched against the stored name. They are escaped with e()
// whenever they are printed.
$first  = isset($_GET['first_name']) ? htmlspecialchars_decode(clean_input($_GET['first_name']), ENT_QUOTES) : '';
$last   = isset($_GET['last_name']) ? htmlspecialchars_decode(clean_input($_GET['last_name']), ENT_QUOTES) : '';
$errors = [];

$where  = '';      // WHERE clause with ? placeholders
$types  = '';      // bind_param type string
$params = [];      // values for the placeholders

if ($query === 'job') {
    if ($jobRef === '') {
        $errors[] = "Please enter a job reference to search for.";
    } elseif (!preg_match('/^[A-Z0-9]{6}$/', $jobRef)) {
        $errors[] = "A job reference must be exactly 6 letters or digits.";
    } else {
        $where    = "WHERE job_reference = ?";
        $types    = 's';
        $params[] = $jobRef;
    }
} elseif ($query === 'name') {
    // The manager can fill in the first name, the last name, or both.
    // Names may only contain letters, spaces, hyphens and apostrophes.
    $namePattern = "/^[A-Za-z' -]{1,20}$/";
    $conditions  = [];

    if ($first === '' && $last === '') {
        $errors[] = "Please enter a first name, a last name, or both.";
    }
    if ($first !== '' && !preg_match($namePattern, $first)) {
        $errors[] = "First name may only contain letters, spaces, hyphens and apostrophes (max 20).";
    }
    if ($last !== '' && !preg_match($namePattern, $last)) {
        $errors[] = "Last name may only contain letters, spaces, hyphens and apostrophes (max 20).";
    }

    if (count($errors) === 0) {
        if ($first !== '') {
            $conditions[] = "first_name LIKE ?";
            $types       .= 's';
            $params[]     = '%' . $first . '%';
        }
        if ($last !== '') {
            $conditions[] = "last_name LIKE ?";
            $types       .= 's';
            $params[]     = '%' . $last . '%';
        }
        $where = "WHERE " . implode(" AND ", $conditions);
    }
} else {
    $query = 'all';
}

// ----------------------------------------------------------
// Build and run the query
// Values are never joined into the SQL string; they are bound
// with a prepared statement to prevent SQL injection.
// ----------------------------------------------------------
$results     = [];
$resultTitle = "All Expressions of Interest";

if ($query === 'job' && count($errors) === 0) {
    $resultTitle = "Expressions of Interest for job " . $jobRef;
} elseif ($query === 'name' && count($errors) === 0) {
    $resultTitle = "Expressions of Interest for applicant \"" . trim($first . ' ' . $last) . "\"";
}

if ($eoiTableExists && count($errors) === 0) {
    $sql  = "SELECT * FROM eoi $where ORDER BY EOInumber ASC";
    $stmt = mysqli_prepare($conn, $sql);

    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $queryResult = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($queryResult)) {
        $results[] = $row;
    }

    mysqli_stmt_close($stmt);
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

    <section aria-labelledby="query-heading" class="manage-panel">
      <h2 id="query-heading">Find applications</h2>

      <div class="query-forms">
        <!-- Option 1: list every EOI -->
        <form method="get" action="manage.php">
          <fieldset>
            <legend>List all EOIs</legend>
            <input type="hidden" name="query" value="all">
            <button type="submit">List all</button>
          </fieldset>
        </form>

        <!-- Option 2: list EOIs for one job reference -->
        <form method="get" action="manage.php">
          <fieldset>
            <legend>List by job reference</legend>
            <input type="hidden" name="query" value="job">
            <label for="job_ref">Job reference</label>
            <input type="text" id="job_ref" name="job_ref" maxlength="6"
                   value="<?php echo e($jobRef); ?>">
            <button type="submit">Search</button>
          </fieldset>
        </form>

        <!-- Option 3: list EOIs by applicant first name, last name or both -->
        <form method="get" action="manage.php">
          <fieldset>
            <legend>List by applicant name</legend>
            <input type="hidden" name="query" value="name">
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" maxlength="20"
                   value="<?php echo e($first); ?>">
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" maxlength="20"
                   value="<?php echo e($last); ?>">
            <button type="submit">Search</button>
          </fieldset>
        </form>
      </div>
    </section>

    <?php if (count($errors) > 0) { ?>
      <div class="error-box" role="alert">
        <?php foreach ($errors as $error) { ?>
          <p><?php echo e($error); ?></p>
        <?php } ?>
      </div>
    <?php } else { ?>

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
    <?php } ?>
  </main>

  <!-- Shared footer -->
  <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>

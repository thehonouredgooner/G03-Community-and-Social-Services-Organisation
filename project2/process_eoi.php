<?php
$currentPage = '';   // not one of the nav pages, so no link is highlighted

// process_eoi.php — handles the Expression of Interest form submission.
// All validation happens here on the server, not in the browser.
// All logic runs first, before any HTML, so header() redirects still work.

require_once("settings.php");

// Week 7 sanitise helper — cleans one value before we use it.
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// STEP 1: Block direct access.
// A direct visit has no JobRef in $_POST, so we send the user back.
if (!isset($_POST['JobRef'])) {
    header("Location: apply.php");
    exit();
}

// Connect to the database.
$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// STEP 2: Create the eoi table if it is not there yet (brief requirement).
$create_table = "CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    job_reference CHAR(6) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender VARCHAR(12) NOT NULL,
    street_address VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state VARCHAR(3) NOT NULL,
    postcode CHAR(4) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skill_maintenance BOOLEAN DEFAULT FALSE,
    skill_event_planning BOOLEAN DEFAULT FALSE,
    skill_advertising BOOLEAN DEFAULT FALSE,
    skill_customer_service BOOLEAN DEFAULT FALSE,
    other_skills TEXT,
    status ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New'
)";
mysqli_query($conn, $create_table);

// STEP 3: Sanitise every text value that came from the form.
$job_reference = sanitise_input($_POST['JobRef'] ?? '');
$first_name    = sanitise_input($_POST['Firstname'] ?? '');
$last_name     = sanitise_input($_POST['Lastname'] ?? '');
$dob           = sanitise_input($_POST['DOB'] ?? '');
$gender        = sanitise_input($_POST['gender'] ?? '');
$street        = sanitise_input($_POST['address'] ?? '');
$suburb        = sanitise_input($_POST['suburb'] ?? '');
$state         = sanitise_input($_POST['state'] ?? '');
$postcode      = sanitise_input($_POST['postcode'] ?? '');
$email         = sanitise_input($_POST['email'] ?? '');
$phone         = sanitise_input($_POST['phone'] ?? '');
$other_skills  = sanitise_input($_POST['otherSkills'] ?? '');

// Checkboxes: if a box was ticked it is in $_POST, otherwise it is not.
// Make sure skills is really an array before we use it.
$skills = (isset($_POST['skills']) && is_array($_POST['skills']))
          ? $_POST['skills'] : array();
$skill_maintenance      = in_array("maintenance", $skills) ? 1 : 0;
$skill_event_planning   = in_array("event-planning", $skills) ? 1 : 0;
$skill_advertising      = in_array("advertising", $skills) ? 1 : 0;
$skill_customer_service = in_array("customer-service", $skills) ? 1 : 0;

// STEP 4: Validate each field. Errors build up in $err_msg.
$err_msg = "";

// Job reference — exactly 6 letters or digits.
if (strlen($job_reference) == 0) {
    $err_msg .= "<p>Please enter the job reference number.</p>";
} else if (strlen($job_reference) != 6) {
    $err_msg .= "<p>Job reference must be exactly 6 characters.</p>";
} else if (!preg_match("/^[A-Za-z0-9]*$/", $job_reference)) {
    $err_msg .= "<p>Job reference must contain only letters and digits.</p>";
}

// First name — letters only, up to 20.
if (strlen($first_name) == 0) {
    $err_msg .= "<p>Please enter your first name.</p>";
} else if (strlen($first_name) > 20) {
    $err_msg .= "<p>First name must be 20 characters or fewer.</p>";
} else if (!preg_match("/^[A-Za-z]*$/", $first_name)) {
    $err_msg .= "<p>First name can only contain letters.</p>";
}

// Last name — same rules.
if (strlen($last_name) == 0) {
    $err_msg .= "<p>Please enter your last name.</p>";
} else if (strlen($last_name) > 20) {
    $err_msg .= "<p>Last name must be 20 characters or fewer.</p>";
} else if (!preg_match("/^[A-Za-z]*$/", $last_name)) {
    $err_msg .= "<p>Last name can only contain letters.</p>";
}

// Date of birth — dd/mm/yyyy, then convert to yyyy-mm-dd for MySQL.
$dob_mysql = "";
if (strlen($dob) == 0) {
    $err_msg .= "<p>Please enter your date of birth.</p>";
} else if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/(19|20)[0-9]{2}$/", $dob)) {
    $err_msg .= "<p>Date of birth must be in dd/mm/yyyy format, including the slashes — for example 09/03/2004.</p>";
} else {
    // Format is valid. Now check it's a real calendar date — this blocks
    // things like 31/02/2004 that the pattern alone would let through.
    $parts = explode("/", $dob);
    $day   = (int)$parts[0];
    $month = (int)$parts[1];
    $year  = (int)$parts[2];

    if (!checkdate($month, $day, $year)) {
        $err_msg .= "<p>That date does not exist. Please check the day and month.</p>";
    } else {
        $dob_mysql = $year . "-" . $parts[1] . "-" . $parts[0];
    }
}

// Gender — one option must be chosen.
if (strlen($gender) == 0) {
    $err_msg .= "<p>Please select your gender.</p>";
}

// Street address.
if (strlen($street) == 0) {
    $err_msg .= "<p>Please enter your street address.</p>";
} else if (strlen($street) > 40) {
    $err_msg .= "<p>Street address must be 40 characters or fewer.</p>";
}

// Suburb.
if (strlen($suburb) == 0) {
    $err_msg .= "<p>Please enter your suburb or town.</p>";
} else if (strlen($suburb) > 40) {
    $err_msg .= "<p>Suburb must be 40 characters or fewer.</p>";
}

// State — the dropdown must not be left on the empty option.
if (strlen($state) == 0) {
    $err_msg .= "<p>Please select your state.</p>";
}

// Postcode — exactly 4 digits.
if (strlen($postcode) == 0) {
    $err_msg .= "<p>Please enter your postcode.</p>";
} else if (strlen($postcode) != 4) {
    $err_msg .= "<p>Postcode must be exactly 4 digits.</p>";
} else if (!preg_match("/^[0-9]*$/", $postcode)) {
    $err_msg .= "<p>Postcode must contain only digits.</p>";
}

// Email — use PHP's built-in email check.
if (strlen($email) == 0) {
    $err_msg .= "<p>Please enter your email address.</p>";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err_msg .= "<p>Email address is not in a valid format.</p>";
}

// Phone — 8 to 12 digits.
if (strlen($phone) == 0) {
    $err_msg .= "<p>Please enter your phone number.</p>";
} else if (strlen($phone) < 8 || strlen($phone) > 12) {
    $err_msg .= "<p>Phone number must be between 8 and 12 digits.</p>";
} else if (!preg_match("/^[0-9]*$/", $phone)) {
    $err_msg .= "<p>Phone number must contain only digits.</p>";
}

// Skills — at least one box must be ticked.
// This is the Part 1 feedback fix: HTML alone could not enforce this.
if (count($skills) == 0) {
    $err_msg .= "<p>Please select at least one skill.</p>";
}

// --- STEP 5 (save to database) goes here next ---
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Application Received | Community Connect</title>
        <meta charset="UTF-8">
        <meta name="description" content="Confirmation of your application to Community Connect.">
        <meta name="keywords" content="application, confirmation, EOI">
        <meta name="author" content="Kevin Lee">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="styles/styles.css">
    </head>

    <body class="page-apply">
        <a class="skip-link" href="#main-content">Skip to main content</a>

        <?php require __DIR__ . '/header.inc'; ?>
        <?php require __DIR__ . '/nav.inc'; ?>

        <main id="main-content">
            <?php

            if ($err_msg != "") {
                // Validation failed — show the errors and a link back to the form.
                echo "<h1>We could not save your application</h1>";
                echo "<p>Please fix the following and submit again:</p>";
                echo $err_msg;
                echo "<p><a href='apply.php'>Back to the application form</a></p>";

            } else {
                // STEP 5: No errors, so save the record.
                // Escape every value to block SQL injection (Week 9 Lab pattern).
                $job_reference = mysqli_real_escape_string($conn, $job_reference);
                $first_name    = mysqli_real_escape_string($conn, $first_name);
                $last_name     = mysqli_real_escape_string($conn, $last_name);
                $gender        = mysqli_real_escape_string($conn, $gender);
                $street        = mysqli_real_escape_string($conn, $street);
                $suburb        = mysqli_real_escape_string($conn, $suburb);
                $state         = mysqli_real_escape_string($conn, $state);
                $postcode      = mysqli_real_escape_string($conn, $postcode);
                $email         = mysqli_real_escape_string($conn, $email);
                $phone         = mysqli_real_escape_string($conn, $phone);
                $other_skills  = mysqli_real_escape_string($conn, $other_skills);

                // Build the INSERT. EOInumber and status are left out on purpose:
                // the database fills EOInumber (AUTO_INCREMENT) and status (DEFAULT 'New').
                $insert_sql = "INSERT INTO eoi (
                    job_reference, first_name, last_name, date_of_birth, gender,
                    street_address, suburb, state, postcode, email, phone,
                    skill_maintenance, skill_event_planning,
                    skill_advertising, skill_customer_service,
                    other_skills
                ) VALUES (
                    '$job_reference', '$first_name', '$last_name', '$dob_mysql', '$gender',
                    '$street', '$suburb', '$state', '$postcode', '$email', '$phone',
                    $skill_maintenance, $skill_event_planning,
                    $skill_advertising, $skill_customer_service,
                    '$other_skills'
                )";

                $insert_result = mysqli_query($conn, $insert_sql);

                if ($insert_result) {
                    // Get the EOInumber the database just generated.
                    $eoi_number = mysqli_insert_id($conn);

                    echo "<h1>Thank you, " . $first_name . "</h1>";
                    echo "<p>Your application has been received.</p>";
                    echo "<p>Your EOI number is <strong>" . $eoi_number . "</strong>.</p>";
                    echo "<p>Please keep this number for your records.</p>";
                    echo "<p><a href='index.php'>Back to Home</a></p>";
                } else {
                    echo "<h1>Something went wrong</h1>";
                    echo "<p>We could not save your application. Please try again later.</p>";
                }
            }

            mysqli_close($conn);

            ?>
        </main>

        <?php require __DIR__ . '/footer.inc'; ?>
    </body>
</html>
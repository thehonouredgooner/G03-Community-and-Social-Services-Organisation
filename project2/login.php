<?php
// login.php - authenticates managers and protects manage.php
session_start();
$currentPage = 'login';
require_once __DIR__ . '/settings.php';

// Already logged in? Go straight to the manager page.
if (!empty($_SESSION['username'])) {
    header('Location: manage.php');
    exit;
}

$error = '';
$max_attempts = 3;   // failed logins before the account is locked
$lock_minutes = 5;   // how long the lock lasts

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (isset($_POST['username']) && is_string($_POST['username'])) ? trim($_POST['username']) : '';
    $password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : '';

    if ($username === '' || $password === '' || strlen($username) > 50) {
        $error = 'Invalid username or password.';
    } else {
        try {
            // Same connection variables as manage.php (defined in settings.php)
            $conn = mysqli_connect($host, $user, $password, $database);
            mysqli_set_charset($conn, 'utf8mb4');

            $stmt = mysqli_prepare($conn,
                "SELECT password_hash, (locked_until IS NOT NULL AND locked_until > NOW()) AS is_locked
                 FROM users WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $account = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            // Verify against a dummy hash when the username doesn't exist, so timing doesn't reveal valid usernames
            $dummy = '$2y$10$1a0u6FtYWpxiw8Y05lK4P.AM74zL77H2KghGaM2/XQFSmA.M8D/g2';
            $valid = password_verify($password, $account ? $account['password_hash'] : $dummy);

            if ($account && $account['is_locked']) {
                $error = 'Too many failed attempts. Please try again in a few minutes.';
            } elseif ($account && $valid) {
                $reset = mysqli_prepare($conn,
                    "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE username = ?");
                mysqli_stmt_bind_param($reset, "s", $username);
                mysqli_stmt_execute($reset);

                session_regenerate_id(true);          // prevents session fixation
                $_SESSION['username'] = $username;    // manage.php checks this
                header('Location: manage.php');
                exit;
            } else {
                if ($account) {
                    $fail = mysqli_prepare($conn,
                        "UPDATE users
                         SET locked_until = IF(failed_attempts + 1 >= ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), locked_until),
                             failed_attempts = IF(failed_attempts + 1 >= ?, 0, failed_attempts + 1)
                         WHERE username = ?");
                    mysqli_stmt_bind_param($fail, "iiis", $max_attempts, $lock_minutes, $max_attempts, $username);
                    mysqli_stmt_execute($fail);
                }
                $error = 'Invalid username or password.'; // same message for every failure
            }
        } catch (Throwable $ex) {
            $error = 'Login is temporarily unavailable. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manager login for Community Connect.">
    <title>Manager Login | Community Connect</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body class="page-login">

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <?php require __DIR__ . '/header.inc'; ?>
    <?php require __DIR__ . '/nav.inc'; ?>

    <main id="main-content">
        <section>
            <h1>Manager Login</h1>

            <?php if ($error !== ''): ?>
                <p role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form method="post" action="login.php" novalidate>
                <p>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" maxlength="50" autocomplete="username" required>
                </p>
                <p>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </p>
                <button type="submit">Log in</button>
            </form>
        </section>
    </main>

    <!-- Shared footer -->
    <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>

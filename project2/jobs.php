<?php
$currentPage = 'jobs';
require_once __DIR__ . '/settings.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function render_items($text) {
    $html = '';
    foreach (preg_split('/\R/', trim((string)$text), -1, PREG_SPLIT_NO_EMPTY) as $item) {
        $html .= '<li>' . e(trim($item)) . "</li>\n";
    }
    return $html;
}

$search = (isset($_GET['q']) && is_string($_GET['q'])) ? mb_substr(trim($_GET['q']), 0, 100) : '';
$jobs = [];
$db_error = false;

try {
    $conn = mysqli_connect($host, $user, $password, $database);
    mysqli_set_charset($conn, 'utf8mb4');

    if ($search === '') {
        $stmt = mysqli_prepare($conn, "SELECT * FROM jobs ORDER BY title");
    } else {
        $like = '%' . addcslashes($search, '%_\\') . '%';
        $stmt = mysqli_prepare($conn,
            "SELECT * FROM jobs
             WHERE title LIKE ? OR job_reference LIKE ? OR description LIKE ?
                OR reports_to LIKE ? OR responsibilities LIKE ?
                OR essential_qualifications LIKE ? OR preferable_qualifications LIKE ?
             ORDER BY title");
        mysqli_stmt_bind_param($stmt, "sssssss", $like, $like, $like, $like, $like, $like, $like);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $jobs[] = $row;
    }
    mysqli_stmt_close($stmt);
} catch (Throwable $ex) {
    $db_error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore current job opportunities at Community Connect.">
    <title>Jobs | Community Connect</title>
    <link rel="stylesheet" href="styles/styles.css">

    <style>
        .job-note {
            font-style: italic;
        }
        
    </style>
</head>

<body class="page-jobs">

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <?php require __DIR__ . '/header.inc'; ?>
    <?php require __DIR__ . '/nav.inc'; ?>

    <main id="main-content">

        <section>
            <h1>Current Job Opportunities</h1>

            <p>
                Join Community Connect and help make a positive difference
                in the lives of individuals, families and communities.
            </p>

            <p>
                We are looking for passionate and dedicated people who want
                to support our community programs and services.
            </p>

            <p class="job-note">Applications are currently open.</p>
            
<form method="get" action="jobs.php" role="search" class="job-search">
    <label for="q">Search positions</label>
    <input type="search" id="q" name="q" maxlength="100"
           value="<?php echo e($search); ?>" placeholder="Title, reference or keyword">
    <button type="submit">Search</button>
    <?php if ($search !== ''): ?><a href="jobs.php">Clear search</a><?php endif; ?>
</form>
            
        </section>


        <section aria-labelledby="available-jobs">
            <h2 id="available-jobs">Available Positions</h2>

           <?php if ($db_error): ?>
                <p>Sorry, job listings are temporarily unavailable. Please try again later.</p>
            <?php elseif (!$jobs): ?>
                <p>No positions found<?php echo $search !== '' ? ' for &quot;' . e($search) . '&quot;' : ''; ?>.
                   <?php if ($search !== ''): ?><a href="jobs.php">View all positions</a><?php endif; ?></p>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                <article>
                    <header>
                        <h3><?php echo e($job['title']); ?></h3>
                        <p><strong>Reference:</strong> <?php echo e($job['job_reference']); ?></p>
                        <p><strong>Salary:</strong> <?php echo e($job['salary_range']); ?></p>
                        <p><strong>Reports to:</strong> <?php echo e($job['reports_to']); ?></p>
                    </header>

                    <section>
                        <h4>Position Description</h4>
                        <p><?php echo e($job['description']); ?></p>
                    </section>

                    <section>
                        <h4>Key Responsibilities</h4>
                        <ol><?php echo render_items($job['responsibilities']); ?></ol>
                    </section>

                    <section>
                        <h4>Essential Requirements</h4>
                        <ul><?php echo render_items($job['essential_qualifications']); ?></ul>
                    </section>

                    <?php if (trim((string)$job['preferable_qualifications']) !== ''): ?>
                    <section>
                        <h4>Preferable Requirements</h4>
                        <ul><?php echo render_items($job['preferable_qualifications']); ?></ul>
                    </section>
                    <?php endif; ?>

                    <p><a href="apply.php">Apply for this position</a></p>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>


        <aside>
            <h2>Why Work With Community Connect?</h2>

            <p>
                Working with Community Connect means contributing to programs
                that make a meaningful difference in the lives of individuals,
                families and local communities.
            </p>

            <h3>What we offer</h3>
            <ul>
                <li>Meaningful community-focused work</li>
                <li>Supportive and inclusive workplace</li>
                <li>Professional development opportunities</li>
                <li>Opportunities to work with volunteers and community partners</li>
                <li>Ongoing learning through community programs and workshops</li>
            </ul>

            <h3>Our workplace values</h3>
            <p>
                We value respect, inclusion, compassion and teamwork. Our staff
                work together to create welcoming services that support people
                from diverse backgrounds and strengthen community connections.
            </p>

            <h3>Interested in joining us?</h3>
            <p>
                If one of our current positions matches your skills and interests,
                we encourage you to submit an application and become part of a
                community making a positive impact every day.
            </p>

            <p>
                Interested in making a difference?
                <a href="apply.php">Submit your application</a> today.
            </p>
        </aside>

    </main>

    <!-- Shared footer -->
    <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>

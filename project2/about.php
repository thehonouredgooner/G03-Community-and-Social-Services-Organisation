
<?php
$currentPage = 'about';

require_once __DIR__ . '/settings.php';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed.");
}

$groupResult = mysqli_query($conn, "SELECT * FROM about_info LIMIT 1");
$group = mysqli_fetch_assoc($groupResult);

$memberResult = mysqli_query($conn, "SELECT * FROM about_members ORDER BY id");

$members = [];

while ($member = mysqli_fetch_assoc($memberResult)) {
    $members[] = $member;
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Meet the student team who designed and built the Community Connect recruitment website.">
  <title>About the Team | Community Connect</title>

  <link rel="stylesheet" href="styles/styles.css">

  <style>
    .page-about .quote-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 1.5rem;
    }

    .page-about .quote-card blockquote {
      margin: 0;
      font-size: 1.05rem;
    }

    .page-about .translation {
      color: #55625B;
      font-size: 0.95rem;
    }
  </style>
</head>

<body class="page-about">
  <a class="skip-link" href="#main-content">Skip to main content</a>

  <?php require __DIR__ . '/header.inc'; ?>
  <?php require __DIR__ . '/nav.inc'; ?>

  <main id="main-content">
    <h1>About the team</h1>

    <section aria-labelledby="group-heading">
      <h2 id="group-heading">Group details</h2>

      <ul class="group-details">
        <li>Group
          <ul>
            <li>Group code: <?php echo htmlspecialchars($group['group_code']); ?></li>
            <li>Group name: <?php echo htmlspecialchars($group['group_name']); ?></li>
            <li>Industry: <?php echo htmlspecialchars($group['industry']); ?></li>

          </ul>
        </li>
        <li>Tutorial class
          <ul>
            <li>Day and time: <?php echo htmlspecialchars($group['tutorial_time']); ?></li>
            <li>Tutor: <?php echo htmlspecialchars($group['tutor']); ?></li>
            <li>Campus: <?php echo htmlspecialchars($group['campus']); ?></li>
          </ul>
        </li>
        <li>Members
          <ul>
            <?php foreach ($members as $member) { ?>
              <li>
                <?php echo htmlspecialchars($member['name']); ?>
                <span class="student-id">
                  <?php echo htmlspecialchars($member['student_id']); ?>
                </span>
              </li>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </section>

    <section aria-labelledby="contrib-heading">
      <h2 id="contrib-heading">Who built what</h2>

      
      <dl class="contributions">
        <?php foreach ($members as $member) { ?>
          <dt>
            <?php echo htmlspecialchars($member['name']); ?>
            <span class="student-id">
              <?php echo htmlspecialchars($member['student_id']); ?>
            </span>
          </dt>

          <dd>
            <strong>Part 1:</strong>
            <?php echo htmlspecialchars($member['part1_contribution']); ?>
          </dd>

          <dd>
            <strong>Part 2:</strong>
            <?php echo htmlspecialchars($member['part2_contribution']); ?>
          </dd>
        <?php } ?>
      </dl>

    </section>

    <section aria-labelledby="quotes-heading">
      <h2 id="quotes-heading">In our own words</h2>

      <p>Something each of us enjoys or feels strongly about.</p>

      <div class="quote-grid"> 
        <?php foreach ($members as $member) { ?>
          <article class="quote-card">
            <h3><?php echo htmlspecialchars($member['name']); ?></h3>

            <blockquote lang="<?php echo htmlspecialchars($member['quote_language']); ?>">
              <p><?php echo htmlspecialchars($member['quote']); ?></p>
            </blockquote>

            <p class="translation" lang="en">
              <?php echo htmlspecialchars($member['quote_translation']); ?>
            </p>
          </article>
        <?php } ?>
      </div>
    </section>

    <section aria-labelledby="photo-heading">
      <h2 id="photo-heading">Our team</h2>


      <figure class="group-figure">
        <img src="<?php echo htmlspecialchars($group['group_photo']); ?>"
            alt="Picture of the four team members"
            style="max-width: 100%;">

        <figcaption>
          <?php echo htmlspecialchars($group['photo_caption']); ?>
        </figcaption>
      </figure>


    <section aria-labelledby="facts-heading">
      <h2 id="facts-heading">Fun facts</h2>

      <table class="team-table">
        <caption>A few things about us</caption>
        <thead>
          <tr>
            <th scope="col">Member</th>
            <th scope="col">First language</th>
            <th scope="col">Fun fact</th>
            <th scope="col">Another fun fact</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $member) { ?>
            <tr>
              <th scope="row">
                <?php echo htmlspecialchars($member['name']); ?>
              </th>
              <td>
                <?php echo htmlspecialchars($member['first_language']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($member['fun_fact1']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($member['fun_fact2']); ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </section>
  </main>

  <!-- Shared footer -->
  <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>
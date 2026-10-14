
<nav class="site-nav" aria-label="Main navigation">
    <ul>
        <li>
            <a href="index.php"
               <?php if ($currentPage === 'index') echo 'aria-current="page"'; ?>>
                Home
            </a>
        </li>

        <li>
            <a href="jobs.php"
               <?php if ($currentPage === 'jobs') echo 'aria-current="page"'; ?>>
                Jobs
            </a>
        </li>

        <li>
            <a href="apply.php"
               <?php if ($currentPage === 'apply') echo 'aria-current="page"'; ?>>
                Apply
            </a>
        </li>

        <li>
            <a href="about.php"
               <?php if ($currentPage === 'about') echo 'aria-current="page"'; ?>>
                About us
            </a>
        </li>
    </ul>
</nav>
</header>

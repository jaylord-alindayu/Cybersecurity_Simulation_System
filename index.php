<?php

require_once __DIR__ . '/config.php';

page_header('Dashboard');

$modules = [

    [
        'caesar.php',
        '🔐',
        'Caesar Cipher',
        'Shift letters by a selected number to encrypt or decrypt text.',
        'CRYPTOGRAPHY'
    ],

    [
        'playfair.php',
        '▦',
        'Playfair Cipher',
        'Build a 5×5 key square and process pairs of letters.',
        'CRYPTOGRAPHY'
    ],

    [
        'sql_injection.php',
        '🗄️',
        'SQL Injection',
        'Compare an intentionally vulnerable local demo with a prepared-statement defense.',
        'WEB SECURITY'
    ],

    [
        'mono.php',
        '🔤',
        'Monoalphabetic Cipher',
        'Replace each plaintext letter using a substitution alphabet.',
        'CRYPTOGRAPHY'
    ],

    [
        'xss.php',
        '</>',
        'Cross-Site Scripting (XSS)',
        'Compare unsafe HTML output with safely escaped user input.',
        'WEB SECURITY'
    ],

    [
        'csrf.php',
        '🛡️',
        'Cross-Site Request Forgery (CSRF)',
        'See how a session token prevents unauthorized state-changing requests.',
        'WEB SECURITY'
    ],

];

?>

<section class="hero">

    <div>

        <span class="pill">
            6 LEARNING MODULES
        </span>

        <h2>
            Learn how ciphers work<br>
            and how web attacks are prevented.
        </h2>

        <p>
            Run each simulation, inspect the result, and read the explanation.
        </p>

    </div>

    <div class="hero-mark">
        SEC<br>
        LAB
    </div>

</section>

<div class="section-title">

    <h2>Simulation modules</h2>

    <span>Choose a module to begin</span>

</div>

<div class="module-grid">

    <?php foreach ($modules as $m): ?>

        <a class="module-card" href="<?= e($m[0]) ?>">

            <div class="module-icon">
                <?= $m[1] ?>
            </div>

            <span class="category">
                <?= e($m[4]) ?>
            </span>

            <h3>
                <?= e($m[2]) ?>
            </h3>

            <p>
                <?= e($m[3]) ?>
            </p>

            <span class="open-link">
                Open simulation →
            </span>

        </a>

    <?php endforeach; ?>

</div>

<div class="notice">

    <strong>Safety note:</strong>

    The SQL Injection, XSS, and CSRF pages are controlled demonstrations.
    They do not attack external websites, execute submitted scripts,
    or change real accounts.

</div>

<?php page_footer(); ?>

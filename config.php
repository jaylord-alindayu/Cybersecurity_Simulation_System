<?php

declare(strict_types=1);

define('APP_NAME', 'Cybersecurity Simulation System');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'cybersecurity_lab');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP MySQL password is blank.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


// DATABASE CONNECTION

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}


// HTML ESCAPING

function e(?string $value): string
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// CSRF TOKEN

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}


// CSRF VALIDATION

function csrf_valid(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals(
            $_SESSION['csrf_token'],
            (string) $_POST['csrf_token']
        );
}


// PAGE HEADER

function page_header(string $title): void
{
    ?>

    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?= e($title) ?> | <?= e(APP_NAME) ?></title>

        <link rel="stylesheet" href="assets/style.css">
    </head>

    <body>

        <header class="topbar">

            <a class="brand" href="index.php">
                🛡️ Cybersecurity Lab
            </a>

            <span class="topnote">
                LOCAL EDUCATIONAL SIMULATOR
            </span>

        </header>

        <div class="shell">

            <aside class="sidebar">

                <div class="side-label">
                    SIMULATIONS
                </div>

                <a href="index.php">⌂ Dashboard</a>

                <a href="caesar.php">01 · Caesar Cipher</a>

                <a href="playfair.php">02 · Playfair Cipher</a>

                <a href="sql_injection.php">03 · SQL Injection</a>

                <a href="mono.php">04 · Monoalphabetic</a>

                <a href="xss.php">05 · XSS</a>

                <a href="csrf.php">06 · CSRF</a>

                

            </aside>

            <main class="content">

                <div class="page-heading">

                    <div>
                        <p class="eyebrow">
                            CYBERSECURITY & CRYPTOGRAPHY
                        </p>

                        <h1><?= e($title) ?></h1>
                    </div>

                </div>

    <?php
}


// PAGE FOOTER

function page_footer(): void
{
    ?>

            </main>

        </div>

        <footer class="footer">
            Cybersecurity Simulation System · Educational use only
        </footer>

    </body>
    </html>

    <?php
}

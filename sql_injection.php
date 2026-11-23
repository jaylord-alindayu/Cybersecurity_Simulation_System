
<?php

require_once __DIR__ . '/config.php';

$unsafeMessage = '';
$safeMessage = '';
$username = '';
$password = '';
$seedError = '';

try {

    $pdo = db();

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lab_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(60) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL
        )
    ");

    $count = (int)$pdo->query(
        "SELECT COUNT(*) FROM lab_users"
    )->fetchColumn();

    if ($count === 0) {

        $st = $pdo->prepare(
            "INSERT INTO lab_users(username, password_hash) VALUES(?, ?)"
        );

        $st->execute([
            'student',
            password_hash('lab123', PASSWORD_DEFAULT)
        ]);
    }

} catch (Throwable $e) {

    $seedError = 'Database connection/setup failed. Import database.sql and check config.php.';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_valid()) {

        $unsafeMessage = $safeMessage = 'Invalid form token.';

    } else {

        $username = (string)($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        // SAFE: parameterized query; user input is never concatenated into SQL.

        try {

            $st = db()->prepare(
                'SELECT id, username, password_hash
                 FROM lab_users
                 WHERE username = :username
                 LIMIT 1'
            );

            $st->execute([
                'username' => $username
            ]);

            $u = $st->fetch();

            $safeMessage = (
                $u && password_verify($password, $u['password_hash'])
            )
                ? 'Protected login: credentials accepted.'
                : 'Protected login: access denied.';

        } catch (Throwable $e) {

            $safeMessage = 'Protected login: request could not be processed.';
        }


        // VULNERABLE LOGIC IS SIMULATED AS A STRING ONLY.
        // It is NEVER executed.

        $unsafeQuery = "SELECT * FROM lab_users WHERE username = '$username' AND password = '$password'";

        $looksLikeInjection = (bool)preg_match(
            '/\b(or|and)\b\s+[\x27"]?\d+[\x27"]?\s*=\s*[\x27"]?\d+|--|#|\/\*/i',
            $username . ' ' . $password
        );

        $unsafeMessage = $looksLikeInjection
            ? 'Vulnerable demo: input resembles an injection payload. In a real vulnerable query, concatenation could change SQL logic. This lab did not execute it.'
            : 'Vulnerable demo: ordinary input was submitted. The unsafe query is shown for learning only.';
    }
}

page_header('SQL Injection');

?>

<div class="intro">

    <p>
        SQL Injection happens when untrusted input is joined directly
        into an SQL statement. This page compares unsafe query
        construction with a secure prepared statement.
    </p>

</div>

<?php if ($seedError): ?>

    <p class="alert error">
        <?= e($seedError) ?>
    </p>

<?php endif; ?>


<div class="two-col">

    <section class="panel">

        <h2>Local login test</h2>

        <form method="post">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <label>Username</label>

            <input
                name="username"
                value="<?= e($username) ?>"
                placeholder="student"
                required
            >

            <label>Password</label>

            <input
                type="text"
                name="password"
                value="<?= e($password) ?>"
                placeholder="lab123"
                required
            >

            <button>Compare behavior</button>

        </form>

        <p class="hint">
            Demo account:
            <b>student</b> /
            <b>lab123</b>
        </p>

        <?php if ($unsafeMessage): ?>

            <div class="alert warning">
                <?= e($unsafeMessage) ?>
            </div>

            <div class="alert success">
                <?= e($safeMessage) ?>
            </div>

        <?php endif; ?>

    </section>


    <aside class="panel">

        <h2>What is different?</h2>

        <h3 class="danger-text">
            Unsafe pattern (do not use)
        </h3>

        <pre><code>$sql = "SELECT * FROM users
WHERE username = '$username'
AND password = '$password'";</code></pre>

        <p>
            The lab displays the idea but never sends
            this unsafe query to MySQL.
        </p>

        <h3 class="success-text">
            Secure pattern
        </h3>

        <pre><code>$stmt = $pdo->prepare(
    'SELECT * FROM users
     WHERE username = :username'
);

$stmt->execute(['username' => $username]);

password_verify($password, $hash);</code></pre>

    </aside>

</div>


<div class="notice">

    Try a normal login first, then test a harmless input such as
    <code>' OR '1'='1</code>.

    The vulnerable result is only a pattern detector;
    the protected login continues to use parameterized SQL
    and password verification.

</div>

<?php page_footer(); ?>

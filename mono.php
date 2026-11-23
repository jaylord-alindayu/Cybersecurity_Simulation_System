<?php

require_once __DIR__ . '/config.php';

$plain = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$default = 'QWERTYUIOPASDFGHJKLZXCVBNM';

function mono_run(string $text, string $key, bool $dec): string
{
    $a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $key = strtoupper(preg_replace('/[^A-Z]/', '', $key));

    if (
        strlen($key) !== 26 ||
        count(array_unique(str_split($key))) !== 26
    ) {
        throw new InvalidArgumentException(
            'The substitution alphabet must contain all 26 letters exactly once.'
        );
    }

    $from = $dec ? $key : $a;
    $to = $dec ? $a : $key;
    $out = '';

    for ($i = 0; $i < strlen($text); $i++) {

        $c = $text[$i];
        $u = strtoupper($c);
        $p = strpos($from, $u);

        if ($p === false) {
            $out .= $c;
        } else {
            $v = $to[$p];

            $out .= ctype_lower($c)
                ? strtolower($v)
                : $v;
        }
    }

    return $out;
}

$key = $default;
$result = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_valid()) {

        $err = 'Invalid form token.';

    } else {

        try {

            $key = (string)($_POST['key'] ?? $default);

            $result = mono_run(
                (string)($_POST['text'] ?? ''),
                $key,
                ($_POST['mode'] ?? '') === 'decrypt'
            );

        } catch (Throwable $e) {

            $err = $e->getMessage();

        }
    }
}

page_header('Monoalphabetic Cipher');

?>

<div class="intro">

    <p>
        A monoalphabetic substitution cipher maps every plaintext letter
        to one fixed ciphertext letter. The same mapping is used
        throughout the message.
    </p>

</div>

<div class="two-col">

    <section class="panel">

        <h2>Substitution simulator</h2>

        <form method="post">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <label>Cipher alphabet (26 unique letters)</label>

            <input
                name="key"
                maxlength="26"
                value="<?= e($key) ?>"
                required
            >

            <small>
                Default mapping: ABCDEFGHIJKLMNOPQRSTUVWXYZ
                → <?= e($key) ?>
            </small>

            <label>Message</label>

            <textarea
                name="text"
                required
                placeholder="Enter a message"
            ><?= e($_POST['text'] ?? '') ?></textarea>

            <label>Operation</label>

            <select name="mode">

                <option value="encrypt">
                    Encrypt
                </option>

                <option
                    value="decrypt"
                    <?= ($_POST['mode'] ?? '') === 'decrypt' ? 'selected' : '' ?>
                >
                    Decrypt
                </option>

            </select>

            <button>Run simulation</button>

        </form>

        <?php if ($err): ?>

            <p class="alert error">
                <?= e($err) ?>
            </p>

        <?php endif; ?>

        <?php if ($result !== ''): ?>

            <div class="result">

                <span>OUTPUT</span>

                <strong>
                    <?= e($result) ?>
                </strong>

            </div>

        <?php endif; ?>

    </section>

    <aside class="panel dark-panel">

        <h2>Mapping example</h2>

        <div class="mapping">

            <span>Plain</span>

            <b>ABCDEFGHIJKLMNOPQRSTUVWXYZ</b>

            <span>Cipher</span>

            <b><?= e($key) ?></b>

        </div>

        <p>
            Unlike Caesar cipher, each letter can map to any other letter,
            not only a shifted neighbor.
        </p>

        <p>
            <b>Security limitation:</b>
            frequency analysis can often reveal patterns in longer messages.
        </p>

    </aside>

</div>

<?php page_footer(); ?>

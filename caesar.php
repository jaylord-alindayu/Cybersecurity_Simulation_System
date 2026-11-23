
<?php

require_once __DIR__ . '/config.php';

function caesar(string $s, int $shift): string
{
    $shift = (($shift % 26) + 26) % 26;
    $out = '';

    for ($i = 0; $i < strlen($s); $i++) {
        $c = $s[$i];

        if ($c >= 'A' && $c <= 'Z') {
            $out .= chr((ord($c) - 65 + $shift) % 26 + 65);
        } elseif ($c >= 'a' && $c <= 'z') {
            $out .= chr((ord($c) - 97 + $shift) % 26 + 97);
        } else {
            $out .= $c;
        }
    }

    return $out;
}

$result = '';
$message = '';
$shift = 3;
$mode = 'encrypt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_valid()) {
        $message = 'Invalid form token. Refresh the page and try again.';
    } else {

        $text = (string)($_POST['text'] ?? '');
        $shift = max(-1000, min(1000, (int)($_POST['shift'] ?? 3)));
        $mode = ($_POST['mode'] ?? 'encrypt') === 'decrypt' ? 'decrypt' : 'encrypt';

        $result = caesar($text, $mode === 'encrypt' ? $shift : -$shift);
    }
}

page_header('Caesar Cipher');

?>

<div class="intro">
    <p>
        A Caesar cipher replaces each letter with another letter a fixed
        number of positions away in the alphabet. Non-letter characters stay unchanged.
    </p>
</div>

<div class="two-col">

    <section class="panel">

        <h2>Try the cipher</h2>

        <form method="post">

            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label>Message</label>

            <textarea name="text" required placeholder="Example: HELLO WORLD"><?= e($_POST['text'] ?? '') ?></textarea>

            <div class="form-row">

                <div>
                    <label>Shift amount</label>
                    <input type="number" name="shift" value="<?= e((string)$shift) ?>" min="-1000" max="1000">
                </div>

                <div>
                    <label>Operation</label>

                    <select name="mode">

                        <option value="encrypt" <?= $mode === 'encrypt' ? 'selected' : '' ?>>
                            Encrypt
                        </option>

                        <option value="decrypt" <?= $mode === 'decrypt' ? 'selected' : '' ?>>
                            Decrypt
                        </option>

                    </select>
                </div>

            </div>

            <button>Run simulation</button>

        </form>

        <?php if ($message): ?>

            <p class="alert error">
                <?= e($message) ?>
            </p>

        <?php endif; ?>

        <?php if ($result !== ''): ?>

            <div class="result">
                <span>OUTPUT</span>
                <strong><?= e($result) ?></strong>
            </div>

        <?php endif; ?>

    </section>

    <aside class="panel dark-panel">

        <h2>How it works</h2>

        <p>
            For encryption, move each alphabetic character forward by the shift.
            For decryption, move it backward.
        </p>

        <div class="equation">
            E(x) = (x + k) mod 26<br>
            D(x) = (x − k) mod 26
        </div>

        <p>
            <b>Example:</b> With shift 3, A becomes D, B becomes E,
            and HELLO becomes KHOOR.
        </p>

    </aside>

</div>

<?php page_footer(); ?>

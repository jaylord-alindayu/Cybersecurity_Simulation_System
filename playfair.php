<?php

require_once __DIR__ . '/config.php';

function pf_square(string $key): array
{
    $key = strtoupper(preg_replace('/[^A-Z]/', '', $key));
    $key = str_replace('J', 'I', $key);

    $chars = [];

    foreach (str_split($key . 'ABCDEFGHIKLMNOPQRSTUVWXYZ') as $c) {

        if (!in_array($c, $chars, true)) {
            $chars[] = $c;
        }
    }

    $sq = array_chunk(array_slice($chars, 0, 25), 5);
    $pos = [];

    foreach ($sq as $r => $row) {

        foreach ($row as $c => $v) {
            $pos[$v] = [$r, $c];
        }
    }

    return [$sq, $pos];
}


function pf_prepare(string $text): array
{
    $s = str_replace(
        'J',
        'I',
        strtoupper(preg_replace('/[^A-Z]/', '', $text))
    );

    $pairs = [];

    for ($i = 0; $i < strlen($s);) {

        $a = $s[$i];
        $b = $s[$i + 1] ?? 'X';

        if ($a === $b) {

            $pairs[] = [$a, 'X'];
            $i++;

        } else {

            $pairs[] = [$a, $b];
            $i += 2;
        }
    }

    return $pairs;
}


function playfair_run(string $text, string $key, bool $decrypt = false): string
{
    [$sq, $p] = pf_square($key);

    $out = '';

    foreach (pf_prepare($text) as [$a, $b]) {

        [$r1, $c1] = $p[$a];
        [$r2, $c2] = $p[$b];

        $step = $decrypt ? 4 : 1;

        if ($r1 === $r2) {

            $out .= $sq[$r1][($c1 + $step) % 5]
                . $sq[$r2][($c2 + $step) % 5];

        } elseif ($c1 === $c2) {

            $out .= $sq[($r1 + $step) % 5][$c1]
                . $sq[($r2 + $step) % 5][$c2];

        } else {

            $out .= $sq[$r1][$c2]
                . $sq[$r2][$c1];
        }
    }

    return $out;
}


$key = 'MONARCHY';
$result = '';
$square = [];
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_valid()) {

        $err = 'Invalid form token.';

    } else {

        $key = (string)($_POST['key'] ?? 'MONARCHY');
        $text = (string)($_POST['text'] ?? '');
        $dec = ($_POST['mode'] ?? 'encrypt') === 'decrypt';

        [$square] = pf_square($key);

        $result = playfair_run($text, $key, $dec);
    }

} else {

    [$square] = pf_square($key);
}

page_header('Playfair Cipher');

?>

<div class="intro">

    <p>
        Playfair encrypts pairs of letters (digraphs) using a 5×5 key square.
        I and J share one cell. Repeated letters in a pair are separated with X.
    </p>

</div>

<div class="two-col">

    <section class="panel">

        <h2>Try the cipher</h2>

        <form method="post">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <label>Keyword</label>

            <input
                name="key"
                value="<?= e($key) ?>"
                required
            >

            <label>Message</label>

            <textarea
                name="text"
                required
                placeholder="Example: INSTRUMENT"
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

                <span>OUTPUT (PREPARED LETTERS)</span>

                <strong>
                    <?= e($result) ?>
                </strong>

            </div>

        <?php endif; ?>

    </section>

    <aside class="panel">

        <h2>5 × 5 key square</h2>

        <div class="matrix">

            <?php foreach ($square as $row): ?>

                <?php foreach ($row as $v): ?>

                    <span>
                        <?= e($v) ?>
                    </span>

                <?php endforeach; ?>

            <?php endforeach; ?>

        </div>

        <h3>Pair rules</h3>

        <ul>

            <li>
                Same row: take the letter to the right (left for decrypt).
            </li>

            <li>
                Same column: take the letter below (above for decrypt).
            </li>

            <li>
                Rectangle: take the other corner in the same row.
            </li>

        </ul>

    </aside>

</div>

<div class="notice">

    Note: Decryption returns prepared text. Filler X characters may need
    to be removed manually because they can also be genuine letters.

</div>

<?php page_footer(); ?>

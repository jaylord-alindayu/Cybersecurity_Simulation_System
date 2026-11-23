
<?php

require_once __DIR__ . '/config.php';

$input = '';
$escaped = '';
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {

    $input = (string)($_POST['comment'] ?? '');

    $escaped = htmlspecialchars(
        $input,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

    $submitted = true;
}

page_header('Cross-Site Scripting (XSS)');

?>

<div class="intro">

    <p>
        XSS occurs when an application places untrusted content into a
        web page as executable markup or script. This simulator shows
        the unsafe string as text only and demonstrates safe output escaping.
    </p>

</div>

<div class="two-col">

    <section class="panel">

        <h2>Enter sample content</h2>

        <form method="post">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <label>Comment or test string</label>

            <textarea
                name="comment"
                placeholder="Example: &lt;script&gt;alert('test')&lt;/script&gt;"
                required
            ><?= e($input) ?></textarea>

            <button>Analyze input</button>

        </form>

        <?php if ($submitted): ?>

            <div class="result">

                <span>SAFE BROWSER OUTPUT</span>

                <strong class="safe-output">
                    <?= e($input) ?>
                </strong>

            </div>

        <?php endif; ?>

    </section>


    <aside class="panel">

        <h2>Comparison</h2>

        <h3 class="danger-text">
            Unsafe approach
        </h3>

        <pre><code>// Do not do this:
echo $userInput;</code></pre>

        <p>
            Raw HTML may be interpreted by the browser.
        </p>

        <h3 class="success-text">
            Safe approach
        </h3>

        <pre><code>echo htmlspecialchars(
    $userInput,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);</code></pre>

        <p>
            Escaping makes special characters display as text
            rather than markup.
        </p>

    </aside>

</div>


<?php if ($submitted): ?>

    <section class="panel">

        <h2>Escaped HTML representation</h2>

        <pre><code><?= e($escaped) ?></code></pre>

        <p class="hint">
            No submitted script is executed in this lab.
        </p>

    </section>

<?php endif; ?>


<?php page_footer(); ?>

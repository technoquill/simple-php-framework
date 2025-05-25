<?php
declare(strict_types=1);

/** @var string $content */
/** @var string $title */

?>
<!doctype html>
<html lang="<?= app()->lang ?>">
<head>
    <meta charset="<?= app()->charset ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?= asset()->includeHeader() ?>
    <?= asset()->addInlineStyle('
          html {}; /** just an example how to use addInlineStyle() */
    ', ['data-style' => 'example']) ?>
    <title><?= $title ?? '' ?></title>
</head>
<body>

<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <header>
                    <br>
                    <a href="<?= route('home.index') ?>">
                        <img class="img-fluid logotype" src="<?= asset()->source('images/logo-equator.svg') ?>" alt="">
                    </a>
                </header>
                <main>
                    <?= $content ?>
                </main>
                <footer>
                    <ul>
                        <li><a href="<?= route('home.index') ?>">Home</a></li>
                        <li><a href="<?= route('home.license') ?>">License</a></li>
                        <li><a href="<?= route('home.welcome', ['name' => 'guest']) ?>">Welcome</a></li>
                        <li><a href="/404">404</a></li>
                    </ul>
                </footer>

            </div>
        </div>
    </div>
</div>

<?= asset()->includeFooter() ?>
<?= asset()->addInlineScript('
          console.log("I am alive!"); // just an example how to use addInlineScript()
    ', ['data-script' => 'example']) ?>
</body>
</html>

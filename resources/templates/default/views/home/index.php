<?php
declare(strict_types=1);

/** @var string $title */

?>

<h1>Welcome to <?= $title ?>!</h1>
<p>Simple PHP Framework is a lightweight and modern micro-framework for developing web applications with PHP.
    Its goal is to provide a clean code structure, convenient routing, rapid startup, and extensibility without
    unnecessary dependencies.</p>
<div class="version"><span>Version: <?= app()->version ?></span></div>
<div class="editor-wrapper">
    <div class="icons"><span></span><span></span><span></span></div>
    <pre class="editor">
<code class="language-php">
    /**
     * @return Response The HTTP response object containing the rendered index page.
    */
    public function index(): Response
    {
        return $this->render('default::home/index', [
            'title' => 'Simple PHP Framework!',
        ]);
    }
</code>
</pre>
<!--    <pre class="version"><code class="language-bash">~$ composer install technoquill/php-simple-framework</code></pre>-->
</div>




<?= $renderer->render('header') ?>
<h1>Bienvenue sur le blog</h1>
<ul>
    <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'zeaezaeze0-9asadezd']); ?>">article1</a></li>
    <li>article1</li>
    <li>article1</li>
    <li>article1</li>
    <li>article1</li>
</ul>
<?= $renderer->render('footer') ?>
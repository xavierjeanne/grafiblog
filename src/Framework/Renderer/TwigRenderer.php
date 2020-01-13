<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    private $twig;
    private $loader;
    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }
    /**
     * add path to load view
     *
     * @param  mixed $namespace
     * @param  mixed $path
     *
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * render the view
     * path can be clarify with namespace via addPath()
     * $this->render('@blog/view')
     * $this->render('view)
     * @param  mixed $view
     * @param  mixed $params
     *
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }

    /**
     * add global variable to all views
     *
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}

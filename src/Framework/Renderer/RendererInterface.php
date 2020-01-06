<?php

namespace Framework\Renderer;

interface RendererInterface
{
    /**
     * add path to load view
     *
     * @param  mixed $namespace
     * @param  mixed $path
     *
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

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
    public function render(string $view, array $params = []): string;

    /**
     * add global variable to all views
     *
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void;
}

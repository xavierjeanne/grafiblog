<?php

namespace Framework;

class Renderer
{
    /**
     * path array
     *
     * @var array
     */
    private $paths = [];

    /**
     * default namespace
     * 
     * @const string
     */
    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * global variable 
     * 
     * @var array
     */
    private $globals = [];

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
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
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
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
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
        $this->globals[$key] = $value;
    }
    /**
     * check if view has namespace
     *
     * @param string $view
     * @return boolean
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }
    /**
     * get namespace of view
     *
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return  substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * replace namespace
     *
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}

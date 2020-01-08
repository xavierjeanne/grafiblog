<?php

namespace Framework\Session;

class ArraySession implements SessionInterface
{
    private $session = [];
    /**
     * retrieve information in session
     *
     * @param string $key
     * @param mixed default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }
        return $default;
    }

    /**
     * add information in session
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    /**
     * delete key session
     *
     * @param string $key
     *
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}

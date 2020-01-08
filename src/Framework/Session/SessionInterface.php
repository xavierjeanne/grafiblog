<?php

namespace Framework\Session;

interface SessionInterface
{
    /**
     * retrieve information in session
     *
     * @param string $key
     * @param mixed default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * add information in session
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * delete key session
     *
     * @param string $key
     *
     */
    public function delete(string $key): void;
}

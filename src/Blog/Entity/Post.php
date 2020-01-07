<?php

namespace App\Blog\Entity;

class Post
{
    public $id;
    public $name;
    public $content;
    public $slug;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new \Datetime($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new \Datetime($this->updated_at);
        }
    }
}

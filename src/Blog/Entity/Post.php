<?php

namespace App\Blog\Entity;

class Post
{
    public $id;
    public $name;
    public $content;
    public $slug;
    public $createdAt;
    public $updatedAt;
    public $categoryName;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new \Datetime($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new \Datetime($this->updated_at);
        }
    }
    public function setCreatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->createdAt = new \Datetime($datetime);
        }
    }
    public function setUpdatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new \Datetime($datetime);
        }
    }
}

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
    public $image;


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
    public function getThumb()
    {
        return '/uploads/posts/' . $this->image;
    }
}

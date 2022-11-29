<?php

namespace App\Model;

/**
 * Data from a search
 * q = Post.title
 * categories = Category.name
 */
class SearchData
{
    /** @var int */
    public $page = 1;

    /** @var string */
    public string $q = '';

    /** @var array */
    public array $categories = [];
}

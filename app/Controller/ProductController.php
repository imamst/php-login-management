<?php

namespace ProgrammerZamanNow\MVC\Controller;

class ProductController
{
    public function categories(string $productId, string $categoryId): void
    {
        echo "Product: <b>$productId</b><br>Category: <b>$categoryId</b>";
    }
}
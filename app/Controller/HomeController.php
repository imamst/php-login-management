<?php

namespace ProgrammerZamanNow\MVC\Controller;

use ProgrammerZamanNow\MVC\App\View;

class HomeController
{
    public function index(): void
    {
        View::render("Home/index", [
            'title' => 'PHP Login Management'
        ]);
    }
}
<?php

namespace ProgrammerZamanNow\MVC\Middleware;

interface Middleware
{
    function before(): void;
}
<?php

namespace ProgrammerZamanNow\MVC\App {

    // untuk mengatasi error phpunit 'cannot modify header...' saat menemui redirect dengan header('Location: /')
    function header(string $value) {
        echo $value;
    }

}

namespace ProgrammerZamanNow\MVC\Service {

    // handle setcookie (header manipulation)
    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}

<?php

namespace ProgrammerZamanNow\MVC;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testRegex()
    {
        $path = "/products/1234/categories/abcd";

        $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

        /**
         * '#' = regex mark
         * '^' = start mark pattern
         * '&' = end mark pattern
         * ([]*) = can be more than one char
         * 0-9 = number
         * a-z = lowercase alphabhet
         * A-Z = uppercase alphabet
         */

        $result = preg_match($pattern, $path, $variables);

        self::assertEquals(1, $result);

        // remove first index
        array_shift($variables);

        var_dump($variables);
    }
}
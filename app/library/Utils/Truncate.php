<?php

namespace Ovnisreales\Utils;

use Transliterator;

/**
 * Truncate component
 *
 * @package Phalcon\Utils
 */
class Truncate
{

    public static function truncar($text, $chars = '30')
    {
        if(strlen($text) > $chars) {
            $text = $text.' ';
            $text = substr($text, 0, $chars);
            $text = substr($text, 0, strrpos($text ,' '));
            $text = $text.'...';
        }
        return $text;
    }

}
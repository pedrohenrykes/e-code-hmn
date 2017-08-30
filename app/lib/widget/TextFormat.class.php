<?php

class TextFormat
{
    private function __construct(){}

    public static function set( $text = null )
    {
        $text = '<font style="color:#F00;font-weight:bold;">' .
        $text . '</font> deve ser preenchido corretamente, pois ';

        return $text;
    }
}

<?php

namespace Blixon\PhpToml;

trait Validators
{
    private static function validateString(string $string): ?string
    {
        $string = trim($string);

        if( ($string[0] != '"' && $string[0] != "'")   ||   ($string[strlen($string)-1] != '"' && $string[strlen($string)-1] != "'") )
            return null;
        
        $string = substr($string, 1, -1); //Cutting off quote characters ( "text"  -->  text ).
        $string = strtr($string, ['\"' => '"', "\'" => "'"]); //Replacing escaped quote characters to their proper form ( te\"xt  -->  te"xt ). 
        return $string;
    }

    private static function validateNumber(string $string): int | float | null
    {
        $string = trim($string);
        $matches = null;
        preg_match("/(-|\+)?(\d*\.?\d*)/", $string, $matches);

        if($matches[2] == "")
            return null;
        elseif($matches[2] == "0")
            return 0;
        
        $multiplier = ($matches[1] == "-" ? -1 : 1);

        return $matches[2] * $multiplier;
        // TODO: MAKE UNDERSCORE DETECTION AND REMOVAL

        // $string = strtr($string, ["_" => ""]);
    } 
};
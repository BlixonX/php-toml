<?php

namespace Blixon\PhpToml;

use Exception;

trait Validators
{
    private static function validateString(string $string): ?string //TODO: Multi-line basic/literal strings
    {
        $string = trim($string);

        if( ($string[0] != '"' && $string[0] != "'")   ||   ($string[strlen($string)-1] != '"' && $string[strlen($string)-1] != "'") )
            return null;
        if( $string[0] != $string[strlen($string)-1] )
            return null;
        
        $string = substr($string, 1, -1); //Cutting off quote characters ( "text"  -->  text ).
        $string = strtr($string, ['\"' => '"', "\'" => "'"]); //Replacing escaped quote characters to their proper form ( te\"xt  -->  te"xt ). 
        
        $string = strtr($string, 
        ["\\t" => "\t",
        "\\n" => "\n",
        "\\f" => "\f",
        "\\r" => "\r",
        "\\\\" => "\\"
        ]); //Other escaped characters

        return $string;
    }

    private static function validateNumber(string $string): int | float | null //TODO: Add eponential value support
    {
        $string = trim($string);
        $matches = null;
        preg_match("/(-|\+)?([0-9_]*\.?[0-9_]*)/", $string, $matches); //separates value into +/- and the number (including underscore separation)

        if(sizeof($matches) < 3)
            return null;

        if($matches[2] == "")
            return null;
        elseif($matches[2] == "0")
            return 0;
        
        $multiplier = ($matches[1] == "-" ? -1 : 1);

        try
        {
            return strtr($matches[2], ["_" => ""]) * $multiplier; //clears value of underscore separators and multiplies by +1 or -1.
        }
        catch(Exception)
        {
            return null;
        }
    }

    private static function validateBoolean(string $string): ?bool
    {
        switch($string)
        {
            case "true":
                return true;
            case "false":
                return false;
            default:
                return null;
        }
    }

    private static function validateBinary(string $string): ?int
    {
        $matches = null;
        preg_match("/(0b)([0-1_]+)/", $string, $matches);
        if(sizeof($matches) == 0)
            return null;
            
        return bindec($matches[2]);
    }
};
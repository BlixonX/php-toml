<?php
namespace Blixon\PhpToml;

require __DIR__ . '/../vendor/autoload.php';

// require "PhpTomlTraits.php";

use Minwork\Helper\Arr;

class PhpToml
{
    use Validators;

    private const EMPTY = 0;
    private const KEYVAL = 1;
    private const OBJ = 2;

    public static function parseToml(string $text): ?array
    {
        $arr = array();
        $currSubArray = "";
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $text) as $line)
        {
            $line = trim($line);
            
            $lineData = self::getLineData($line);

            switch ($lineData["code"])
            {
                case self::EMPTY:
                    break;
                case self::KEYVAL:
                    $arr = Arr::set($arr, ($currSubArray ? $currSubArray."." : "").$lineData["key"], $lineData["val"]);
                    break;
                case self::OBJ:
                    $arr = Arr::set($arr, $lineData["obj"], null);
                    $currSubArray = $lineData["obj"];
                    break;

            }
        }
        return $arr; 
    }

    public static function getToml(string $path)
    {
        return file_get_contents($path);
    }
    
    private static function getLineData(string $line)
    {
        if(strlen($line) == 0 || $line[0] == "#")
            return [ "code" => self::EMPTY ];



        $keyVal = null;
        preg_match("/^(.*?)=(.*?)$/", $line, $keyVal);

        if(sizeof($keyVal) > 0)
        {
            $key = trim($keyVal[1]);

            if( $key[0] == $key[strlen($key)-1] && ($key[0] == '"' || $key[0] == "'") ) //Removing quotation marks on the keys
                $key = substr($key, 1, -1);


            $postValidationValue = 
            self::validateString      ($keyVal[2]) ??
            self::validateBinary      ($keyVal[2]) ??
            self::validateOctal       ($keyVal[2]) ??
            self::validateHexadecimal ($keyVal[2]) ??
            self::validateNumber      ($keyVal[2]) ?? 
            self::validateBoolean     ($keyVal[2]) ??
            "NULL";
            
            return [
                "code" => self::KEYVAL,
                "key" => $key,
                "val" => $postValidationValue
            ];
        }
        


        $objName = null;
        preg_match("/^\[(.*?)\]/", $line, $objName);

        if(sizeof($objName) > 0)
        {
            return [
                "code" => self::OBJ,
                "obj" => $objName[1]
            ];
        }
    }


}
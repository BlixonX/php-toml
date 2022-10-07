<?php

use Blixon\PhpToml\PhpToml;

function preEcho($text)
{
    echo <<<PREECHO
    <pre>$text</pre>
    PREECHO;
}

$tomlText = PhpToml::getToml("./config.toml");

preEcho($tomlText);

$tomlParsed = PhpToml::parseToml($tomlText);

preEcho(print_r($tomlParsed, true));

// echo $tomlParsed["sus"]["mogus"]["hey"];
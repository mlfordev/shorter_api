<?php

function d($data)
{
    echo Phact\Dumper\VarDumper::dump($data);
    die();
}
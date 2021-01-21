<?php

namespace App\Core;


/**
 * Class Command
 * @package App\Core
 */
abstract class Command
{
    abstract function run(): void;
}
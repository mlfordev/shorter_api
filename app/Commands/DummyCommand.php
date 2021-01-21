<?php

namespace App\Commands;


use App\Core\Command;
use App\Core\Params;

/**
 * Class ParseCommand
 * @package App\Commands
 */
class DummyCommand extends Command
{
    /**
     * ParseCommand constructor.
     */
    public function __construct()
    {
        $params = Params::getInstance();
    }

    function run(): void
    {
        // TODO: Implement run() method.
    }
}

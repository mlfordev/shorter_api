<?php

namespace App\Core\Routers;

class CliRouter extends Router
{
    /**
     * @return void
     */
    public function handleRequest(): void
    {
        $argv = $_SERVER['argv'];

        $commandName = 'App\\Commands\\' . $argv[1] . 'Command';
        $action = 'action' . ($argv[2] ?? 'Index');

        if (class_exists($commandName)) {
            $command = new $commandName;

            if (method_exists($command, $action)) {
                $command->$action();
                exit(0);
            }
        }
    }


}
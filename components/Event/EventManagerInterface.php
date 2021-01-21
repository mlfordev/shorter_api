<?php

namespace Phact\Event;

interface EventManagerInterface
{
    public function on($name, $callback, $sender = null, $priority = 0);

    public function trigger($name, $params = array(), $sender = null, $callback = null);
}
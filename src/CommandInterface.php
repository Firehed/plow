<?php

namespace Firehed\Plow;

interface CommandInterface
{

    /**
     * @return array<string>|string The command, excluding "plow"
     */
    public function getCommandName();

}

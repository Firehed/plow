<?php

namespace Firehed\Plow;

interface CommandInterface
{

    /**
     * @return string The command, excluding "plow"
     */
    public function getCommandName();

}

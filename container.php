<?php

use Pimple\Container;
use Symfony\Component\Console\Output;

$container = new Container();

$container['console_verbosity'] = Output\OutputInterface::VERBOSITY_NORMAL;

$container['console_output_interface'] = function($c) {
    return new Output\ConsoleOutput($c['console_verbosity']);
};

$container['output_interface'] = function($c) {
    return new Firehed\Plow\Output($c['console_output_interface']);
};

return $container;

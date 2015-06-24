<?php

namespace Firehed\Plow;

class Plow implements CommandInterface
{

    use CommandTrait;

    public function getBanner()
    {
        return 'Usage: %s [options]';
    }

    public function getCommandName()
    {
        return '';
    }


    public function getDescription() {
        return '';
    }

    public function getOptions()
    {
        return [
            Option::withCount('l', 'list')
                ->setDescription('List all of the available commands'),
        ];
    }

    public function execute()
    {
        $this->output->writeLine("PLOW 0.0.0");
        if ($this->getOption('list')) {
            return $this->listCommands();
        }

        // Placeholder for other options or subcommands

        return $this->listCommands();
    }

    private function listCommands()
    {

        $cfg = PlowCLI::loadCommands();
        $out = [];
        foreach ($cfg['classes'] as $className) {
            if (!class_exists($className)) continue;
            $class = new $className();
            $cname = current((array)$class->getCommandName());
            $synopsis = $class->getSynopsis();
            $out[$cname] = $synopsis;
        }

        // Find the longest command string to align output
        $widest = array_reduce(array_keys($out), function($carry, $item) {
            return max($carry, strlen($item));
        }, 0);

        $this->output->writeLine('Available commands:');
        foreach ($out as $command => $synopsis) {
            $this->output->writeLine('%s%s',
                str_pad($command, $widest + 5),
                $synopsis);
        }
        return 0;
    }



}

<?php

namespace Firehed\Plow;

class PlowCLI
{

    /**
     * Cached command Trie
     */
    private static $commands = null;

    /**
     * Absolute path to plow
     */
    private static $plow = null;

    /**
     * Get the command Trie
     * @return array
     */
    public static function loadCommands()
    {
        if (self::$commands === null) {
            // This automatically resolves relative and absolute paths... nifty
            self::$plow = realpath($_SERVER['_']);
            // Plow will be at vendor/firehed/plow/bin/plow
            // Commands are installed to vendor/firehed/plow/commands/...
            $plow_basedir = dirname(dirname(self::$plow));
            $cmd_dir = $plow_basedir.'/commands';
            $file = $cmd_dir.'/commands.json';
            if (!file_exists($file) || !is_readable($file)) {
                // Warn about no commands?
                return self::getDefaultCommands();
            }
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            self::$commands = $data;

        }
        return self::$commands;
    }

    public static function getCommandTrie()
    {
        $command_data = self::loadCommands();
        return $command_data['command_trie'];
    }

    /**
     * Get the default option flags for all commands
     *
     * @return array<Ulrichsg\Getopt\Option>
     */
    public static function getDefaultOptions()
    {
        return [
            Option::withCount('h', 'help')
                ->setDescription('Print help and exit'),
            Option::withCount('q', 'quiet')
                ->setDescription('Suppress all output'),
            Option::withCount('v', 'verbose')
                ->setDescription('Increase verbosity with -v, -vv, or -vvv'),
            Option::withCount('V', 'version')
                ->setDescription('Print command version and exit'),
        ];
    }

    private static function getDefaultCommands()
    {
        return [
            'package-info' => [],
            'classes' => [],
            'command_trie' => [
                'plow' => [
                    '*'=> 'Firehed\\Plow\\Plow',
                ],
            ],
            '@gener'.'ated' => time(),
        ];
    }

}

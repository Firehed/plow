<?php

namespace Firehed\Plow;

use Symfony\Component\Console\Output\OutputInterface as COI;
use UnexpectedValueException;

class Utilities
{

    /**
     * Convert CLI flags for quiet and verbose into Symfony Console output
     * verbosity levels
     *
     * @param int $quiet -q/--quiet param
     * @param int $verbosity -v/--verbose param
     * @return int OutputInterface::VERBOSITY_xxx constant
     */
    public static function parseVerbosity($quiet, $verbosity) {
        if ($quiet && $verbosity) {
            throw new UnexpectedValueException(
                'Either "quiet" or "verbose" may be provided, but not both.');
        }
        if ($quiet) {
            return COI::VERBOSITY_QUIET;
        }
        switch ($verbosity) {
        case 0:
            return COI::VERBOSITY_NORMAL;
        case 1:
            return COI::VERBOSITY_VERBOSE;
        case 2:
            return COI::VERBOSITY_VERY_VERBOSE;
        case $verbosity >= 3:
            return COI::VERBOSITY_DEBUG;
        }
    }

    /**
     * @param array $trie The structure to search
     * @param &array $argv The command argument structure
     * @return string The class name found in the trie
     */
    public static function searchTrieFromArgv(array $trie, array &$argv)
    {
        // Search the trie with ARGV, and modify it so that the subcommands all
        // look like one giant command as related to argument passing.
        $argv[0] = 'plow';
        $cn = [];
        $i = 0;
        do {
            $cmd = strtolower($argv[0]);
            if (isset($trie[$cmd])) {
                $trie = $trie[$cmd];
                if (isset($trie['*'])) {
                    $class = $trie['*'];
                    $matched = $i;
                }
                $i++;
                $cn[] = array_shift($argv);
            }
            else {
                break;
            }
        } while ($argv);

        $matched++; // Turn into a count from an offset
        $extras = array_slice($cn, $matched);
        $actual_command = array_slice($cn, 0, $matched);

        // Prepend them back on to the original argv
        while ($extras) {
            array_unshift($argv, array_pop($extras));
        }
//        array_unshift($argv, implode(' ', $actual_command));
        $_SERVER['PHP_SELF'] = implode(' ', $actual_command);

        return $class;

    }


}

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


}

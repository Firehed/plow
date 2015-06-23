<?php

namespace Firehed\Plow;

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option as GO;

/**
 * Convenience class that exists solely as a factory to the underlying Option
 * class. Simplifies the API very slightly, but mostly serves to not have to
 * remember a second namespace to import.
 */
class Option
{

    /**
     * @param ?string Single ASCII character for short flag
     * @param ?string Single ASCII word for long flag
     * @return Ulrichsg\Getopt\Option
     */
    public static function withCount($short = null, $long = null)
    {
        return GO::create($short, $long)->setDefaultValue(0);
    }

    /**
     * @param ?string Single ASCII character for short flag
     * @param ?string Single ASCII word for long flag
     * @return Ulrichsg\Getopt\Option
     */
    public static function withOptionalValue($short = null, $long = null)
    {
        return GO::create($short, $long, Getopt::OPTIONAL_ARGUMENT);
    }

    /**
     * @param ?string Single ASCII character for short flag
     * @param ?string Single ASCII word for long flag
     * @return Ulrichsg\Getopt\Option
     */
    public static function withRequiredValue($short = null, $long = null)
    {
        return GO::create($short, $long, Getopt::REQUIRED_ARGUMENT);
    }

}

<?php

namespace Firehed\Plow;

/**
 * This roughly translates the Symfony\Component\Console\Output\OutputInterface
 * into one far more useful in day-to-day use: it handles all of the verbosity
 * logic, rather than having to scatter it throughout your code. This comes at
 * a trivial performance cost (some unnecessary string handling), but with the
 * benefit of much more readable code.
 */
interface OutputInterface
{

    /**
     * Output the message to the console
     * @param string message
     * @return self
     */
    public function write($msg); // normal
    public function writeLine($msg); // normal

    public function writeVerbose($msg); // v
    public function writeVerboseLine($msg); // v

    public function writeVeryVerbose($msg); // vv
    public function writeVeryVerboseLine($msg); // vv

    public function error($msg); // stderr
    public function errorLine($msg); // stderr

    /**
     * Debug any data type - it will attempt to coerce the data to a string:
     * __toString() on objects where the method exists, (string) on scalars, and
     * print_r on anything else.
     *
     * Only shows in -vvv level
     *
     * Newlines are *always* added, unlike all other methods.
     *
     * @param mixed message
     * @return self
     */
    public function debug($msg); // vvv

}

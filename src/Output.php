<?php

namespace Firehed\Plow;

use Symfony\Component\Console\Output\OutputInterface as OI;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * Acts as an adapter for the Symfony\Component\Console\Output\OutputInterface,
 * since it only half-manages the verbosity level. This keeps approximately the
 * same output semantics, but puts all of the "if output verbosity is >= X"
 * logic in a single place.
 *
 * @see Symfony\Component\Console\Output\OutputInterface
 */
class Output implements OutputInterface
{
    private $output;

    public function __construct(ConsoleOutputInterface $output)
    {
        $this->output = $output;
    }

    public function error($format, ...$args)
    {
        $msg = sprintf($format, ...$args);
        $this->output->getErrorOutput()->write($msg);
        return $this;
    }

    public function errorLine($format, ...$args)
    {
        $msg = sprintf($format.PHP_EOL, ...$args);
        $this->output->getErrorOutput()->write($msg);
        return $this;
    }

    public function write($format, ...$args)
    {
        return $this->writeIf($format, OI::VERBOSITY_NORMAL, $args);
    }

    public function writeLine($format, ...$args)
    {
        return $this->writeIf($format.PHP_EOL, OI::VERBOSITY_NORMAL, $args);
    }

    public function writeVerbose($format, ...$args)
    {
        return $this->writeIf($format, OI::VERBOSITY_VERBOSE, $args);
    }

    public function writeVerboseLine($format, ...$args)
    {
        return $this->writeIf($format.PHP_EOL, OI::VERBOSITY_VERBOSE, $args);
    }

    public function writeVeryVerbose($format, ...$args)
    {
        return $this->writeIf($format, OI::VERBOSITY_VERY_VERBOSE, $args);
    }

    public function writeVeryVerboseLine($format, ...$args)
    {
        return $this->writeIf($format.PHP_EOL, OI::VERBOSITY_VERY_VERBOSE, $args);
    }

    public function debug($mixed_msg)
    {
        if (OI::VERBOSITY_DEBUG > $this->output->getVerbosity()) {
            return;
        }
        // Cast output to string
        if (is_scalar($mixed_msg)) {
            $msg = $mixed_msg;
        } elseif (is_object($mixed_msg) && method_exists($mixed_msg, '__toString')) {
            $msg = (string) $mixed_msg;
        } else {
            $msg = print_r($mixed_msg, true);
        }
        $this->output->write($msg.PHP_EOL);
        return $this;
    }

    /**
     * Conditionally write to the output based on the current verbosity level
     *
     * @param string sprintf-style format string
     * @param int minimum verbosity level
     * @param array<mixed> sprintf-style arguments
     * @return self
     */
    private function writeIf($format, $level, array $args)
    {
        if ($level > $this->output->getVerbosity()) {
            return;
        }
        $this->output->write(sprintf($format, ...$args));
        return $this;
    }

}

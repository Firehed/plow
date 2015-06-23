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

    public function error($msg)
    {
        $this->output->getErrorOutput()->write($msg);
        return $this;
    }

    public function errorLine($msg)
    {
        $this->output->getErrorOutput()->write($msg.PHP_EOL);
        return $this;
    }

    public function write($msg)
    {
        return $this->writeIf($msg, OI::VERBOSITY_NORMAL);
    }

    public function writeLine($msg)
    {
        return $this->writeIf($msg.PHP_EOL, OI::VERBOSITY_NORMAL);
    }

    public function writeVerbose($msg)
    {
        return $this->writeIf($msg, OI::VERBOSITY_VERBOSE);
    }

    public function writeVerboseLine($msg)
    {
        return $this->writeIf($msg.PHP_EOL, OI::VERBOSITY_VERBOSE);
    }

    public function writeVeryVerbose($msg)
    {
        return $this->writeIf($msg, OI::VERBOSITY_VERY_VERBOSE);
    }

    public function writeVeryVerboseLine($msg)
    {
        return $this->writeIf($msg.PHP_EOL, OI::VERBOSITY_VERY_VERBOSE);
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
            $msg = print_r($msg, true);
        }
        $this->output->write($msg.PHP_EOL);
        return $this;
    }

    private function writeIf($msg, $level)
    {
        if ($level > $this->output->getVerbosity()) {
            return;
        }
        $this->output->write($msg);
        return $this;
    }

}

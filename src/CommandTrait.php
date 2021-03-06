<?php

namespace Firehed\Plow;

use UnexpectedValueException;

/**
 * Trait to implement some sane default values for CommandInterface
 *
 * It also adds the convenience method `usageError` and accessors for the
 * operands and options.
 */
trait CommandTrait
{

    protected $operands = [];
    protected $optionValues = [];
    protected $output;

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Default to the standard usage banner
     *
     * @return string
     */
    public function getBanner()
    {
        return '';
    }

    /**
     * Null-safe operand access, by numeric index
     *
     * @return ?string
     */
    protected function getOperand($idx)
    {
        return isset($this->operands[$idx]) ? $this->operands[$idx] : null;
    }

    /**
     * Null-safe option access, by string index. Maps 1:1 with the short and
     * long values return by `getOptions`
     *
     * @return ?mixed
     */
    protected function getOption($option)
    {
        return isset($this->optionValues[$option]) ? $this->optionValues[$option] : null;
    }

    /**
     * Treat the first line of the description as the command's synopsis.
     *
     * @return string
     */
    public function getSynopsis()
    {
        return current(explode("\n", $this->getDescription()));
    }

    /**
     * Injection point for CLI operands
     *
     * @param array<string> $values The CLI operands
     * @return self
     */
    public function setOperands(array $values)
    {
        $this->operands = $values;
        return $this;
    }

    /**
     * Injection point for CLI options
     *
     * @param array<mixed> $values The CLI options
     * @return self
     */
    public function setOptionValues(array $values)
    {
        $this->optionValues = $values;
        return $this;
    }

    /**
     * Injection point for the OutputInterface object
     *
     * @param OutputInterface $out The OutputInterface object
     * @return self
     */
    public function setOutput(OutputInterface $out)
    {
        $this->output = $out;
        return $this;
    }

    /**
     * Convenience method to throw an UnexpectedValueException, which will
     * subsequently be caught by the main script and print help text
     *
     * @throws UnexpectedValueException
     */
    protected function usageError($format, ...$args)
    {
        $msg = sprintf($format, ...$args);
        throw new UnexpectedValueException($msg);
    }

}

<?php

namespace Firehed\Plow;

interface CommandInterface
{

    /**
     * Run the command
     * @return int CLI exit code
     */
    public function execute();

    /**
     * @return array Command aliases
     */
    public function getAliases();

    /**
     * Generate the 'usage' banner. Can include a single %s for the command
     * name. Does NOT require a trailing newline. An empty return value will
     * cause the default banner to be used.
     *
     * Default: 'Usage: %s [options] [operands]'
     *
     * @return string The usage banner
     */
    public function getBanner();

    /**
     * @return string The command, excluding "plow"
     */
    public function getCommandName();

    /**
     * Command description. It should look roughly like a git commit message,
     * where the first line is a short synopsis, with the message body
     * containing more detailed information.
     *
     * @return string The description
     */
    public function getDescription();

    /**
     * Desired CLI options. Plow uses the Getopt library, so this function must
     * return an array of Getopt Options.
     *
     * The following options will automatically be added, and must not be
     * included:
     * -h/--help
     * -v/--verbose
     * -q/--quiet
     * -V/--version
     *
     * The \Firehed\Plow\Option class provides factory methods around the Getopt
     * library for convenience.
     *
     * @see http://ulrichsg.github.io/getopt-php/advanced/option-descriptions.html
     * @return array<\Ulrichsg\Getopt\Option>
     */
    public function getOptions();

    /**
     * Synopsis of the command, which is displayed in the command list. Should
     * not be more than one line.
     *
     * @return string Brief command description
     */
    public function getSynopsis();

    /**
     * Inject the CLI operands to the command
     * @param array<string> all of the space-separated arguments
     * @return self
     */
    public function setOperands(array $values);

    /**
     * Inject the option values provided at the command line
     * @see http://ulrichsg.github.io/getopt-php/basic/retrieving-values.html
     * @param array<string,mixed> key-value pairs
     * @return self
     */
    public function setOptionValues(array $values);

    /**
     * Inject the output interface
     * @param OutputInterface
     * @return self
     */
    public function setOutput(OutputInterface $output);

}

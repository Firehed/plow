<?php

namespace Firehed\Plow;

use Ulrichsg\Getopt\Getopt;

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
            $cmd_dir = $plow_basedir.'/installed_commands';
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

    /**
     * Constructor
     *
     * @param array $argv The CLI argument values
     * @param OutputInterface $console The output stream
     */
    public function __construct(array $argv, OutputInterface $console) {
        $this->argv = $argv;
        $this->console = $console;
    }

    /**
     * The main logic block for dispatching the CLI to the implementation
     * classes. Assuming it makes it all the way to the actual method cleanly,
     * its return code will be propagated up.  Otherwise, RuntimeExceptions exit
     * 1 (user error), LogicExceptions exit 2 (programmer error), and everything
     * else exits 3 (doom)
     *
     * @return int The intended exit status cide
     */
    public function run() {
        $trie = self::getCommandTrie();
        $class = Utilities::searchTrieFromArgv($trie, $this->argv);
        $cmd = new $class();
        $banner = $cmd->getBanner();

        try {
            $opt = new Getopt();
            $opt->addOptions($cmd->getOptions());
            $opt->addOptions(self::getDefaultOptions());
            if ($banner) $opt->setBanner($banner.PHP_EOL);
            $opt->parse(implode(' ', $this->argv));
        } catch (\UnexpectedValueException $e) {
            // Unexpected CLI arguments
            $this->console->exception($e);
            $this->console->writeLine($opt->getHelpText());
            return 1;
        } catch (\InvalidArgumentException $e) {
            // Command is broken - most likely duplicated arguments
            $this->console->exception($e);
            return 2;
        } catch (\Exception $e) {
            // Catch-all,
            $this->console->exception($e);
            return 3;
        }

        // Unfortunately we can't easily do this earlier. Native getopt() is
        // useless on all subcommands, and the class implementation screams
        // about unexpected values.
        $v = Utilities::parseVerbosity($opt['q'],$opt['v']);
        $this->console->setVerbosity($v);

        if ($opt['help']) {
            return $this->showHelp($cmd, $opt->getHelpText());
        }
        if ($opt['version']) {
            return $this->showVersion($cmd);
        }

        try {
            return $cmd
                ->setOutput($this->console)
                ->setOperands($opt->getOperands())
                ->setOptionValues($opt->getOptions())
                ->execute();
        } catch (\RuntimeException $e) {
            $this->console->exception($e);
            $this->console->writeLine($opt->getHelpText());
            return 1;
        } catch (\LogicException $e) {
            $this->console->exception($e);
            return 2;
        } catch (\Exception $e) {
            $this->console->exception($e);
            return 3;
        }
    }

    /**
     * Write the help text to the console
     *
     * @param CommandInterface $command The command for which to display text
     * @param string $helptext FIXME this needs refactoring...
     * @return int 0, always
     */
    private function showHelp(CommandInterface $command, $helpText)
    {
        $this->console->writeLine($command->getDescription())
            ->writeLine('')
            ->writeLine($helpText);

        return 0;
    }
    /**
     * Write the version to the console
     *
     * @param CommandInterface $command The command the version will be
     *                                  displayed for
     * @return int 0, always
     */
    private function showVersion(CommandInterface $command)
    {
        $this->console->writeLine('plow%s%s %s',
            $command->getCommandName() ? ' ' : '',
            $command->getCommandName(),
            $command->getVersion());
        return 0;
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

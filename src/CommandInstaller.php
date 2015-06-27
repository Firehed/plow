<?php

namespace Firehed\Plow;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Repository\InstalledRepositoryInterface;


class CommandInstaller extends LibraryInstaller
{

    const COMMAND_DIR = 'vendor/firehed/plow/installed_commands/';
    const COMMAND_FILE = 'commands.json';
    const TRIE_VALUE_KEY = '*';

    private $loaded = false;
    private $madeChanges = false;

    /**
     * Array of packages to the Plow command classes they provide
     */
    private $packages = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(
        IOInterface $io,
        Composer $composer,
        $type = 'library',
        Filesystem $filesystem = null
    ) {
        parent::__construct($io, $composer, $type, $filesystem);
        $this->loadCommandList();
    }

    public function __destruct()
    {
        if ($this->madeChanges || !$this->loaded) {
            $this->writeCommandList();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'plow-command' === $packageType;
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo,
        PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->addCommandsFromPackage($package);
    }

    /**
     * {@inheritDoc}
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        parent::update($repo, $initial, $target);
        $this->removeCommandsFromPackage($initial);
        $this->addCommandsFromPackage($target);
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::uninstall($repo, $package);
        $this->removeCommandsFromPackage($package);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageBasePath(PackageInterface $package)
    {
        return self::COMMAND_DIR.$package->getPrettyName();
    }

    private function loadCommandList()
    {
        $file = self::COMMAND_DIR.self::COMMAND_FILE;
        if (!file_exists($file) || !is_readable($file)) {
            return;
        }
        $data = json_decode(file_get_contents($file), true);
        $this->packages = $data['package-info'];
        $this->loaded = true;
    }

    private function writeCommandList()
    {
        $data = [
            'package-info' => $this->packages,
        ];
        $classes = [];
        foreach ($this->packages as $package => $packageData) {
            $classes = array_merge($classes, $packageData['classes']);
        }
        $commandMap = self::getCommandMapFromClasses($classes);
        $trie = self::buildTrieFromCommandMap($commandMap);
        $data['classes'] = $classes;
        $data['command_trie'] = $trie;
        $data['@gener'.'ated'] = time();
        #mkdir(self::COMMAND_DIR, 0755, true);
        file_put_contents(self::COMMAND_DIR.self::COMMAND_FILE, json_encode($data));
    }

    private static function getCommandMapFromClasses(array $classes)
    {
        // Ok, so this is sketchy, but there's not a great way around it.
        // Basically, Composer has dumped the autoloader files, but they're not
        // actually in-memory for this command. Pull them in so that they can
        // be loaded and inspected.
        //
        // Because this is run from `composer install` (and friends), the CWD
        // should always be set correctly for this to work as expected.
        require 'vendor/autoload.php';
        $commands = [];
        foreach ($classes as $className) {
            if (!class_exists($className)) {
                continue;
            }
            $rc = new \ReflectionClass($className);
            if (!$rc->implementsInterface('Firehed\Plow\CommandInterface')) {
                continue;
            }
            $commandClass = new $className();
            $names = array_merge(
                [$commandClass->getCommandName()],
                $commandClass->getAliases()
            );
            $commands[$className] = $names;
        }
        return $commands;
    }

    private static function buildTrieFromCommandMap(array $commandMap)
    {
        $trie = [];
        foreach ($commandMap as $className => $commands) {
            foreach ($commands as $command) {
                $commandWords = explode(' ', strtolower('plow '.$command));
                $pos =& $trie;
                // Index into the output array by word
                foreach ($commandWords as $word) {
                    if (!isset($pos[$word])) {
                        $pos[$word] = [];
                    }
                    $pos =& $pos[$word];
                }
                $pos[self::TRIE_VALUE_KEY] = $className;
            }
        }
        $trie['plow'][self::TRIE_VALUE_KEY] = 'Firehed\Plow\Plow';
        return $trie;
    }

    private function addCommandsFromPackage(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (empty($extra['plow']) || !is_array($extra['plow'])) {
            return;
        }

        $this->packages[$package->getPrettyName()] = [
            'version' => $package->getPrettyVersion(),
            'classes' => $extra['plow'],
        ];
        $this->madeChanges = true;
    }

    private function removeCommandsFromPackage(PackageInterface $package)
    {
        unset($this->packages[$package->getPrettyName()]);
        $this->madeChanges = true;
    }

}

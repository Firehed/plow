<?php

namespace Firehed\Plow;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Repository\InstalledRepositoryInterface;


class CommandInstaller extends LibraryInstaller
{

    const COMMAND_DIR = 'plow-commands/';
    const COMMAND_FILE = 'commands.json';
    private $commands = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null)
    {
        parent::__construct($io, $composer, $type, $filesystem);
        $this->loadCommandList();
    }

    public function __destruct()
    {
        $this->writeCommandList();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
      return 'plow-command' === $packageType;
    }

    /**
     * Installs specific package.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $package package instance
     */
    public function install(InstalledRepositoryInterface $repo,
        PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->addCommandsFromPackage($package);
    }

    /**
     * Updates specific package.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $initial already installed package version
     * @param PackageInterface             $target  updated version
     *
     * @throws InvalidArgumentException if $initial package is not installed
     */
    public function update(InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->addCommandsFromPackage($package);
    }



    /**
     * Uninstalls specific package.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $package package instance
     */
    public function uninstall(InstalledRepositoryInterface $repo,
        PackageInterface $package)
    {
        parent::uninstall($repo, $package);
        $this->removeCommandsFromPackage($package);
    }

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
        $this->commands = $data['package-info'];
    }

    private function writeCommandList()
    {
        $data = [
            'package-info' => $this->commands,
        ];
        $classes = [];
        foreach ($this->commands as $package => $commands) {
            $classes = array_merge($classes, $commands);
        }
        foreach ($classes as $className) {
            // class_exists and check that it implements the right interface
            //
            // Instanciate class
            //
            // Call a method to get the actual command name
            //
            // Write this to the file in such a way that the actual binary can
            // dispatch back to that class
        }

        $data['classes'] = $classes;
        $data['@gener'.'ated'] = time();
        file_put_contents(self::COMMAND_DIR.self::COMMAND_FILE, json_encode($data));
    }

    private function addCommandsFromPackage(PackageInterface $package)
    {
        $this->commands[$package->getPrettyName()] = (array)$package->getExtra();
    }

    private function removeCommandsFromPackage(PackageInterface $package)
    {
        unset($this->commands[$package->getPrettyName()]);
    }


}

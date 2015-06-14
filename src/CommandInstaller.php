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
    private $classes = [];
    private $madeChanges = false;

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
        if ($this->madeChanges) {
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
        $this->addCommandsFromPackage($package);
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
        $this->classes = $data['package-info'];
    }

    private function writeCommandList()
    {
        $data = [
            'package-info' => $this->classes,
        ];
        $classes = [];
        foreach ($this->classes as $package => $packageClasses) {
            $classes = array_merge($classes, $packageClasses);
        }
        $data['classes'] = $classes;
        $data['@gener'.'ated'] = time();
        file_put_contents(self::COMMAND_DIR.self::COMMAND_FILE, json_encode($data));
    }

    private function addCommandsFromPackage(PackageInterface $package)
    {
        $this->classes[$package->getPrettyName()] = (array)$package->getExtra();
        $this->madeChanges = true;
    }

    private function removeCommandsFromPackage(PackageInterface $package)
    {
        unset($this->classes[$package->getPrettyName()]);
        $this->madeChanges = true;
    }

}

<?php

namespace Firehed\Plow;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class CommandInstaller extends LibraryInstaller
{

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
      return 'plow-command' === $packageType;
    }
}

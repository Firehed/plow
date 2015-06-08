<?php
namespace Firehed\Plow;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class PluginInstallerPlugin implements PluginInterface
{

    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new CommandInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

}

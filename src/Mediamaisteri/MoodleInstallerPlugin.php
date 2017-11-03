<?php

namespace Mediamaisteri;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class MoodleInstallerPlugin implements PluginInterface {

    /**
     *
     */
    public function activate(Composer $composer, IOInterface $io) {
        $installer = new MoodleInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}

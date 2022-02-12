<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use SimpleFormsBundle\Tools\Installer;

class SimpleFormsBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    protected function getComposerPackageName(): string
    {
        return 'spotbot2k/pimcore-simple-forms';
    }

    // public function getInstaller()
    // {
    //     return $this->container->get(Installer::class);
    // }
}

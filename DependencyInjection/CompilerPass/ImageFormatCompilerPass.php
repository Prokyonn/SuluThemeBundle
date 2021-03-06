<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass;

use Liip\ThemeBundle\ActiveTheme;
use Sulu\Bundle\MediaBundle\DependencyInjection\AbstractImageFormatCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass loads all image formats defined in the configuration files in all the themes.
 */
class ImageFormatCompilerPass extends AbstractImageFormatCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function getFiles(ContainerBuilder $container)
    {
        $files = [];

        /** @var ActiveTheme $activeTheme */
        $activeTheme = $container->get('liip_theme.active_theme');
        $bundles = $container->getParameter('kernel.bundles');
        $configPath = 'config/image-formats.xml';

        foreach ($activeTheme->getThemes() as $theme) {
            foreach ($bundles as $bundleName => $bundle) {
                $bundleReflection = new \ReflectionClass($bundle);
                $fileName = $bundleReflection->getFileName();

                if (!$fileName) {
                    continue;
                }

                $path = sprintf(
                    '%s/Resources/themes/%s/%s',
                    dirname($fileName),
                    $theme,
                    $configPath
                );

                if (file_exists($path)) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }
}

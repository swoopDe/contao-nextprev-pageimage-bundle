<?php

declare(strict_types=1);

namespace Swoopde\ContaoNextPrevPageimageBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


/**
 * in einem Contao/Symfony-Bundle werden Klassen nicht automatisch als Services gescannt, solange du keine services.yaml (oder Extension) mit einem Service-Resource-Import definierst.
 * Das Attribut AsFrontendModule wirkt nur, wenn Symfony die Klasse überhaupt als Service kennt.
 * Wichtig: Der Klassenname muss zum Bundle passen. Bei ContaoNextPrevPageimageBundle ist die Extension ContaoNextPrevPageimageExtension.
 */
class ContaoNextPrevPageimageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}

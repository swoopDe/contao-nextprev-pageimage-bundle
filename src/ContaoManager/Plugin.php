<?php
namespace Swoopde\ContaoNextPrevPageimageBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
//use Terminal42\Pageimage\Terminal42PageimageBundle;


class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('Swoopde\\ContaoNextPrevPageimageBundle\\ContaoNextPrevPageimageBundle')
            ->setLoadAfter([ContaoCoreBundle::class,
//                Terminal42PageimageBundle::class
            ]),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Swoopde\ContaoNextPrevPageimageBundle\Twig;

use Composer\InstalledVersions;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class ContaoVersionExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        // contao/core-bundle ist in Contao 4 & 5 vorhanden
        $version = null;

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('contao/core-bundle')) {
            $version = InstalledVersions::getPrettyVersion('contao/core-bundle'); // z.B. "4.13.42" oder "5.3.8"
        }

        $major = 0;
        if (is_string($version) && preg_match('/^(\d+)/', $version, $m)) {
            $major = (int) $m[1];
        }

        return [
            'contao_core_bundle_version' => $version,
            'contao_major' => $major,              // 4 oder 5
            'is_contao5' => $major >= 5,
        ];
    }
}

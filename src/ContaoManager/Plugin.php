<?php

declare(strict_types=1);


namespace Tastaturberuf\ContaoFormContentBundle\ContaoManager;


use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Tastaturberuf\ContaoFormContentBundle\TastaturberufContaoFormContentBundle;


class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(TastaturberufContaoFormContentBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}

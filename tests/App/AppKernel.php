<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Tests\App;

use imissyouso\CsvAggregatorBundle\CsvAggregatorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new CsvAggregatorBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }
}

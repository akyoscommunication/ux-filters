<?php

namespace Akyos\UXFilters;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UXFilters extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}

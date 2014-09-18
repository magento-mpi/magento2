<?php

namespace Magento\Tools\SampleData\Module\ConfigurableProduct;

use Magento\Tools\SampleData\SetupInterface;

class Setup implements SetupInterface
{
    protected $productSetup;

    public function __construct(
        Setup\Product $productSetup
    ) {
        $this->productSetup = $productSetup;
    }

    public function run()
    {
        $this->productSetup->run();
    }
}

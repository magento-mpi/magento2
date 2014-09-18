<?php

namespace Magento\Tools\SampleData\Module\Catalog;

use Magento\Tools\SampleData\SetupInterface;

class Setup implements SetupInterface
{
    protected $categorySetup;

    protected $attributeSetup;

    public function __construct(
        Setup\Category $categorySetup,
        Setup\Attribute $attributeSetup
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
    }

    public function run()
    {
        $this->attributeSetup->run();
        $this->categorySetup->run();
    }
}

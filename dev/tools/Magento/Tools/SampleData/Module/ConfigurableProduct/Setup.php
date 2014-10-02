<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData\Module\ConfigurableProduct;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * @param Setup\Product $productSetup
     */
    public function __construct(
        Setup\Product $productSetup
    ) {
        $this->productSetup = $productSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->productSetup->run();
    }
}

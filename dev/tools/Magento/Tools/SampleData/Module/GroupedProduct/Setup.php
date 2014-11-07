<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\GroupedProduct;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for GroupedProduct module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for grouped products
     *
     * @var Setup\Product
     */
    protected $groupedProduct;

    /**
     * @param Setup\Product $groupedProduct
     */
    public function __construct(
        Setup\Product $groupedProduct
    ) {
        $this->groupedProduct = $groupedProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->groupedProduct
            ->run();
    }
}

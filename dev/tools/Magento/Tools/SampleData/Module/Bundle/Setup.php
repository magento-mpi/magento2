<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Bundle;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Bundle module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for bundle products
     *
     * @var Setup\Product
     */
    protected $bundleProduct;

    /**
     * Setup class for catalog products
     *
     * @var \Magento\Tools\SampleData\Module\Catalog\Setup\Product
     */
    protected $catalogProduct;

    /**
     * @param Setup\Product $bundleProduct
     * @param \Magento\Tools\SampleData\Module\Catalog\Setup\Product $catalogProduct
     */
    public function __construct(
        Setup\Product $bundleProduct,
        \Magento\Tools\SampleData\Module\Catalog\Setup\Product $catalogProduct
    ) {
        $this->bundleProduct = $bundleProduct;
        $this->catalogProduct = $catalogProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->catalogProduct
            ->setFixtures(['Bundle/yoga_bundle_options.csv'])
            ->run();
        $this->bundleProduct
            ->run();
    }
}

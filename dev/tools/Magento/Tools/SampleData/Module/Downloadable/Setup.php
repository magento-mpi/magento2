<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Downloadable;

/**
 * Launches setup of sample data for downloadable module
 */
class Setup extends \Magento\Tools\SampleData\Module\Catalog\Setup
{
    /**
     * Setup class for products
     *
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * @param \Magento\Tools\SampleData\Module\Catalog\Setup\Category $categorySetup
     * @param \Magento\Tools\SampleData\Module\Catalog\Setup\Attribute $attributeSetup
     * @param Setup\Product $productSetup
     */
    public function __construct(
        \Magento\Tools\SampleData\Module\Catalog\Setup\Category $categorySetup,
        \Magento\Tools\SampleData\Module\Catalog\Setup\Attribute $attributeSetup,
        Setup\Product $productSetup
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
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

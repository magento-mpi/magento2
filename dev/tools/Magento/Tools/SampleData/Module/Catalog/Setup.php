<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for catalog module
 * @package Magento\Tools\SampleData\Module\Catalog
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for category
     *
     * @var Setup\Category
     */
    protected $categorySetup;

    /**
     * Setup class for product attributes
     *
     * @var Setup\Attribute
     */
    protected $attributeSetup;

    /**
     * Setup class for products
     *
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * Constructor
     *
     * @param Setup\Category $categorySetup
     * @param Setup\Attribute $attributeSetup
     * @param Setup\Product $productSetup

     */
    public function __construct(
        Setup\Category $categorySetup,
        Setup\Attribute $attributeSetup,
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
        $this->attributeSetup->run();
        $this->categorySetup->run();
        $this->productSetup->run();
    }
}

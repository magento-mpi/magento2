<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\ConfigurableProduct\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Test Coverage for CreateConfigurableProductEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Two simple products are created.
 * 2. Configurable attribute with two options is created
 * 3. Configurable attribute added to Default template
 *
 * Steps:
 * 1. Go to Backend
 * 2. Open Product -> Catalog
 * 3. Click on narrow near "Add Product" button
 * 4. Select Configurable Product
 * 5. Fill in data according to data sets
 *  5.1 If field "attributeNew/dataSet" is not empty - search created attribute by putting it's name to variation Search field.
 *  5.2 If "attribute/dataSet" is not empty- create new Variation Set
 * 6. Save product
 * 7. Perform all assertions
 *
 * @group Configurable Product (MX)
 * @ZephyrId MAGETWO-26041
 */
class CreateConfigurableProductEntityTest extends Injectable
{
    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $productIndex;

    /**
     * Page to create a product
     *
     * @var CatalogProductNew
     */
    protected $productNew;

    /**
     * Injection data
     *
     * @param CatalogProductIndex $productIndex
     * @param CatalogProductNew $productNew
     * @return void
     */
    public function __inject(CatalogProductIndex $productIndex, CatalogProductNew $productNew)
    {
        $this->productIndex = $productIndex;
        $this->productNew = $productNew;
    }

    /**
     * Test create catalog Configurable product run
     *
     * @param  ConfigurableProductInjectable $product
     * @return void
     */
    public function test(ConfigurableProductInjectable $product)
    {
        // Steps
        $this->productIndex->open();
        $this->productIndex->getGridPageActionBlock()->addProduct('configurable');
        $this->productNew->getForm()->fill($product);
        $this->productNew->getFormPageActions()->save($product);
    }
}

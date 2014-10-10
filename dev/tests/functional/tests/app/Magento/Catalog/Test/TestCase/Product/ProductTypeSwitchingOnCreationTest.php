<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Creation for ProductTypeSwitchingOnCreation
 *
 * Test Flow:
 * 1. Open backend
 * 2. Go to Products > Catalog
 * 3. Start create product from preconditions (according dataSet)
 * 4. Fill data from dataSet
 * 5. Save
 * 6. Perform all assertions
 *
 * @group Products_(MX)
 * @ZephyrId MAGETWO-29398
 */
class ProductTypeSwitchingOnCreationTest extends Injectable
{
    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Page to create a product
     *
     * @var CatalogProductNew
     */
    protected $catalogProductNew;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Injection data
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductNew $catalogProductNew
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductNew $catalogProductNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductNew = $catalogProductNew;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run product type switching on creation test
     *
     * @param string $createProduct
     * @param string $product
     * @return array
     */
    public function test($createProduct, $product)
    {
        // Steps
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActionBlock()->addProduct($createProduct);
        list($fixture, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
        $this->catalogProductNew->getProductForm()->fill($product);
        $this->catalogProductNew->getFormPageActions()->save($product);

        return ['product' => $product];
    }
}

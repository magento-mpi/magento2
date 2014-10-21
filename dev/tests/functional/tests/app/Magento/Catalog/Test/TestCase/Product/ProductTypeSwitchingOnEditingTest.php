<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Test Creation for ProductTypeSwitchingOnEditing
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create product according to dataSet
 *
 * Steps:
 * 1. Open backend
 * 2. Go to Products > Catalog
 * 3. Open created product in preconditions
 * 4. Perform Actions from dataSet
 * 5. Fill data from dataSet
 * 6. Save
 * 7. Perform all assertions
 *
 * @group Products_(MX)
 * @ZephyrId MAGETWO-29633
 */
class ProductTypeSwitchingOnEditingTest extends Injectable
{
    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Page to update a product
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

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
     * @param CatalogProductEdit $catalogProductEdit
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductEdit = $catalogProductEdit;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run product type switching on creation test
     *
     * @param string $productOrigin
     * @param string $product
     * @param string $deleteVariations
     * @return array
     */
    public function test($productOrigin, $product, $deleteVariations)
    {
        // Preconditions
        list($fixtureClass, $dataSet) = explode('::', $productOrigin);
        $productOrigin = $this->fixtureFactory->createByCode(trim($fixtureClass), ['dataSet' => trim($dataSet)]);
        $productOrigin->persist();

        // Steps
        $this->catalogProductIndex->open();
        $filter = ['sku' => $productOrigin->getSku()];
        $this->catalogProductIndex->getProductGrid()->searchAndOpen($filter);
        list($fixtureClass, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode(trim($fixtureClass), ['dataSet' => trim($dataSet)]);
        $this->catalogProductEdit->getProductForm()->fill($product);
        $this->deleteVariations($deleteVariations);
        $this->catalogProductEdit->getFormPageActions()->save($product);

        return ['product' => $product];
    }

    /**
     * Delete variations
     *
     * @param string $deleteVariations
     * @return void
     */
    protected function deleteVariations($deleteVariations)
    {
        if ($deleteVariations !== '-') {
            $this->catalogProductEdit->getProductForm()->openTab('product-details');
            $this->catalogProductEdit->getProductForm()->openTab('variations');
            /** @var Config $variationsTab */
            $variationsTab = $this->catalogProductEdit->getProductForm()->getTabElement('variations');
            $variationsTab->deleteVariations();
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CrossSellProducts;

/**
 * Class AddCrossSellEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create cross cell products.
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Products > Catalog
 * 3. Click Add new product
 * 4. Fill data from dataSet
 * 5. Save product
 * 6. Perform all assertions
 *
 * @group Cross-sells_(MX)
 * @ZephyrId MAGETWO-29081
 */
class AddCrossSellEntity extends Injectable
{
    /**
     * Catalog product index page on backend
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Catalog product view page on backend
     *
     * @var CatalogProductNew
     */
    protected $catalogProductNew;

    /**
     * Inject data
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductNew $catalogProductNew
     * @return void
     */
    public function __inject(CatalogProductIndex $catalogProductIndex, CatalogProductNew $catalogProductNew)
    {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductNew = $catalogProductNew;
    }

    /**
     * Run test add cross sell entity
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    public function test(CatalogProductSimple $product)
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActionBlock()->addProduct('simple');
        $this->catalogProductNew->getProductForm()->fill($product);
        $this->catalogProductNew->getFormPageActions()->save();

        /** @var CrossSellProducts $crossSellProducts*/
        $crossSellProducts = $product->getDataFieldConfig('cross_sell_products')['source'];
        return ['sellingProducts' => $crossSellProducts->getProducts()];
    }
}

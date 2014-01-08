<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Bundle\Test\Fixture\Bundle;

class EditBundleTest extends Functional
{
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Edit bundle
     *
     * @dataProvider createDataProvider
     * @ZephyrId MAGETWO-12842
     * @ZephyrId MAGETWO-12841
     */
    public function testEditBundle($fixture)
    {
        //Data
        /** @var $product \Magento\Bundle\Test\Fixture\Bundle */
        /** @var $editProduct \Magento\Bundle\Test\Fixture\Bundle */
        $product = Factory::getFixtureFactory()->$fixture();
        $product->switchData('bundle');
        $product->persist();
        $editProduct = Factory::getFixtureFactory()->$fixture();
        $editProduct->switchData('bundle_edit_required_fields');

        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $gridBlock = $productGridPage->getProductGrid();
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $productBlockForm = $editProductPage->getProductBlockForm();
        $cachePage = Factory::getPageFactory()->getAdminCache();

        $productGridPage->open();
        $gridBlock->searchAndOpen(array(
            'sku' => $product->getProductSku(),
            'type' => 'Bundle Product'
        ));
        $productBlockForm->fill($editProduct);
        $productBlockForm->save($editProduct);
        //Verifying
        $editProductPage->getMessagesBlock()->assertSuccessMessage();
        // Flush cache
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        //Verifying
        $this->assertOnGrid($editProduct);
        $this->assertOnCategory($editProduct, $product->getCategoryName());
    }

    public function createDataProvider()
    {
        return array(
            array('getMagentoBundleBundleFixed'),
            array('getMagentoBundleBundleDynamic')
        );
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param Bundle $product
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
    }

    /**
     * @param Bundle $product
     * @param string $categoryName
     */
    protected function assertOnCategory($product, $categoryName)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($categoryName);
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $this->assertSame($product->getProductName(), $productViewBlock->getProductName());
        $this->assertEquals($product->getProductPrice(), $productViewBlock->getProductPrice());
    }
}

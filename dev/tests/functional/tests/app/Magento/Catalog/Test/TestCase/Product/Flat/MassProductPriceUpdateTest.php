<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product\Flat;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\SimpleProduct;

/**
 * Product massaction with enabled flat
 *
 * Class MassProductPriceUpdateTest
 * @package Magento\Catalog\Test\TestCase\Product\Flat
 */
class MassProductPriceUpdateTest extends Functional
{
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();

        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_product_flat');
        $config->persist();
    }

    public function testMassAction()
    {
        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple');
        $product->persist();

        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $gridBlock = $productGridPage->getProductGrid();
        $productGridPage->open();
        $gridBlock->updateAttributes(array(array(
            'sku' => $product->getProductSku()
        )));

        $updateProductPrice = Factory::getFixtureFactory()->getMagentoCatalogProductAttribute();
        $updateProductPrice->switchData('price_massaction');

        $attributeMassactionPage = Factory::getPageFactory()->getCatalogProductActionAttributeEdit();
        $formBlock = $attributeMassactionPage->getAttributesBlockForm();
        $formBlock->enablePriceEdit();
        $formBlock->fill($updateProductPrice);
        $formBlock->save();

        /**
         * Workaround for 'wait' construction
         */
        sleep(5);

        $productGridPage->getMessageBlock()->assertSuccessMessage();

        $this->assertTrue($this->_isOnGrid(array(
            'sku' => $product->getProductSku(),
            'price_from' => $updateProductPrice->getData('fields/price/value'),
            'price_to' => $updateProductPrice->getData('fields/price/value')
        )));

        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();

        $this->_assertOnCategory($product, '$' . $updateProductPrice->getData('fields/price/value'));
    }

    protected function _isOnGrid($productData)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $gridBlock->search($productData);
        unset($productData['price_to']);
        $productData['price_from'] = '$' . $productData['price_from'];
        return $gridBlock->isRowVisible($productData, false);
    }

    protected function _assertOnCategory($product, $productPrice)
    {
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();

        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());

        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $this->assertEquals($productPrice, $productListBlock->getPrice($product->getProductId()));
    }
}

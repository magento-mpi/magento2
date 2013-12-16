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

namespace Magento\GiftCard\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\GiftCard\Test\Fixture\GiftCard;

/**
 * Class testCreate for creating Gift Card
 *
 * @package Magento\GiftCard\Test\TestCase
 */
class testCreate extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Create Gift Card required field only, with assigning to category
     * Virtual type, open amount without min and max restrictions
     *
     * @ZephyrId MAGETWO-13618
     */
    public function testCreate()
    {
        //Data
        $giftcard = Factory::getFixtureFactory()->getMagentoGiftCardGiftCard();
        $giftcard->switchData('virtual_open_amount');
        //Pages & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('giftcard');
        $productBlockForm->fill($giftcard);
        $productBlockForm->save($giftcard);
        //Verification
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verification
        $this->assertOnGrid($giftcard);
        $this->assertOnCategory($giftcard);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param GiftCard $product
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
    }

    /**
     * Assert displaying Gift Card in category on frontend
     *
     * @param GiftCard $product
     */
    protected function assertOnCategory($product)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()),
            'Product is absent on category page');
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $giftCardBlock = $productPage->getGiftCardBlock();
        $this->assertTrue($giftCardBlock->isOpenAmount(), 'Open Amount field is absent');
        $this->assertTrue($giftCardBlock->isGiftCardNotPhysical(), 'Fields are not corresponded to Virtual Card');
    }
}

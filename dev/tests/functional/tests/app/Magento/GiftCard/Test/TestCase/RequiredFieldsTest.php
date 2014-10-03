<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\GiftCard\Test\Fixture\GiftCard;

/**
 * Class testCreate for creating Gift Card
 */
class RequiredFieldsTest extends Functional
{
    /**
     * Login into backend area before test
     *
     * @return void
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
     * @return void
     */
    public function testCreate()
    {
        //Data
        $giftcard = Factory::getFixtureFactory()->getMagentoGiftCardGiftCard();
        $giftcard->switchData('virtual_open_amount');
        //Pages & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productForm = $createProductPage->getProductForm();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getGridPageActionBlock()->addProduct('giftcard');
        $productForm->fill($giftcard);
        $createProductPage->getFormPageActions()->save();
        //Verification
        $createProductPage->getMessagesBlock()->waitSuccessMessage();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->waitSuccessMessage();
        //Verification
        $this->assertOnGrid($giftcard);
        $this->assertOnCategory($giftcard);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param GiftCard $product
     * @return void
     */
    protected function assertOnGrid(GiftCard $product)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(['sku' => $product->getSku()]));
    }

    /**
     * Assert displaying Gift Card in category on frontend
     *
     * @param GiftCard $product
     */
    protected function assertOnCategory(GiftCard $product)
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
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getName()),
            'Product is absent on category page'
        );
        $productListBlock->openProductViewPage($product->getName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getName(), $productViewBlock->getProductName());
        $giftCardBlock = $productPage->getGiftCardBlock();
        $this->assertTrue($giftCardBlock->isAmountInputVisible(), 'Open Amount field is absent');
        $this->assertTrue($giftCardBlock->isGiftCardNotPhysical(), 'Fields are not corresponded to Virtual Card');
    }
}

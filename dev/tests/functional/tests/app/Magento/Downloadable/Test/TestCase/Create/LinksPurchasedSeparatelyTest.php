<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\TestCase\Create;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Downloadable\Test\Fixture\DownloadableProduct;

class LinksPurchasedSeparatelyTest extends Functional
{
    /**
     * Product fixture
     *
     * @var DownloadableProduct
     */
    protected $product;

    protected function setUp()
    {
        $this->product = Factory::getFixtureFactory()
            ->getMagentoDownloadableDownloadableProductLinksPurchasedSeparately();
        $this->product->switchData('downloadable');

        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Creating Downloadable product with required fields only and assign it to the category
     *
     * @ZephyrId MAGETWO-13595
     */
    public function test()
    {
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->init($this->product);
        $productBlockForm = $createProductPage->getProductBlockForm();

        $createProductPage->open();
        $productBlockForm->fill($this->product);
        $productBlockForm->save($this->product);

        $createProductPage->getMessagesBlock()->assertSuccessMessage();

        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();

        $this->assertOnBackend();
        $this->assertOnFrontend();
    }

    /**
     * Assert existing product on admin product grid
     */
    protected function assertOnBackend()
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $this->product->getProductSku())));
    }

    /**
     * Assert product data on category and product pages
     */
    protected function assertOnFrontend()
    {
        $product = $this->product;
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();

        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());

        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $productListBlock->openProductViewPage($product->getProductName());

        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertEquals($product->getProductPrice(), $productViewBlock->getProductPrice());

        $productPage->getDownloadableLinksBlock()
            ->check([['title' => $product->getData('fields/downloadable/link/0/title/value')]]);
        $this->assertEquals(
            (int)$product->getProductPrice() + $product->getData('fields/downloadable/link/0/price/value'),
            $productViewBlock->getProductPrice()
        );
    }
}

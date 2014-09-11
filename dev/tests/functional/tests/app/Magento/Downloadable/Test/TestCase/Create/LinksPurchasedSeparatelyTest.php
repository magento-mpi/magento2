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

/**
 * Class LinksPurchasedSeparatelyTest
 */
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
     * @return void
     */
    public function test()
    {
        $createProductPage = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage->open();
        $createProductPage->getGridPageActionBlock()->addProduct('downloadable');

        $createProductPageNew = Factory::getPageFactory()->getCatalogProductNew();
        $productBlockForm = $createProductPageNew->getProductForm();

        $category = $this->product->getCategories()['category'];
        $productBlockForm->fill($this->product, null, $category);
        $createProductPageNew->getFormPageActions()->save();

        $createProductPageNew->getMessagesBlock()->assertSuccessMessage();

        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();

        $this->assertOnBackend();
        $this->assertOnFrontend();
    }

    /**
     * Assert existing product on admin product grid
     *
     * @return void
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
     *
     * @return void
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
        $this->assertTrue($productListBlock->isProductVisible($product->getName()));

        $productListBlock->openProductViewPage($product->getName());
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getName(), $productViewBlock->getProductName());
        $this->assertEquals(
            number_format($product->getProductPrice(), 2),
            $productViewBlock->getPriceBlock()->getPrice()
        );

        $productDownloadableLinks = $product->getData('fields/downloadable_links/value/downloadable/link');
        $pageDownloadableOptions = $productViewBlock->getOptions($product)['downloadable_links'];
        $pageDownloadableLinks = $pageDownloadableOptions['downloadable']['link'];
        foreach ($productDownloadableLinks as $key => $link) {
            $this->assertEquals(
                $pageDownloadableLinks[$key]['title'],
                $link['title']
            );
            $this->assertEquals(
                $pageDownloadableLinks[$key]['price'],
                $link['price']
            );
        }
    }
}

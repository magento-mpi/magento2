<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class SearchTest
 * Searching product in the Frontend via quick search
 *
 */
class SearchTest extends Functional
{
    /**
     * Using quick search to find the product
     *
     * @ZephyrId MAGETWO-12420
     */
    public function testSearchProductFromHomePage()
    {
        //Preconditions
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_mysql_search');
        $config->persist();

        //Data
        $productFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $productFixture->switchData('simple');
        $productFixture->persist();
        $productName = $productFixture->getName();
        $productSku = $productFixture->getSku();

        //Pages & Blocks
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $resultPage = Factory::getPageFactory()->getCatalogsearchResult();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productListBlock = $resultPage->getListProductBlock();
        $viewBlock = $productPage->getViewBlock();

        //Steps
        $homePage->open();
        $homePage->getSearchBlock()->search($productSku);

        //Verifying
        $this->assertTrue($productListBlock->isProductVisible($productName), 'Product was not found.');
        $productListBlock->openProductViewPage($productName);
        $this->assertEquals($productName, $viewBlock->getProductName(), 'Wrong product page has been opened.');
    }
}

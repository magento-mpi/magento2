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

namespace Magento\Catalog\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class SearchTest
 * Searching product in the Frontend via quick search
 *
 * @package Magento\Catalog\Test\TestCase
 */
class SearchTest extends Functional
{
    /**
     * Search product on frontend by product name
     */
    public function testProductSearch()
    {
        //Data
        $productFixture = Factory::getFixtureFactory()->getMagentoCatalogProduct()->switchData('simple');
        $productFixture->persist();
        $productName = $productFixture->getProductName();

        //Pages
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $resultPage = Factory::getPageFactory()->getCatalogsearchResult();

        //Steps
        $homePage->open();
        $homePage->getSearchBlock()->search($productName);

        //Verifying
        $this->assertTrue($resultPage->getListProductBlock()->isProductVisible($productName));
    }
}

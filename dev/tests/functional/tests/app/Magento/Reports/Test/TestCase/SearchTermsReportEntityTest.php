<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Reports\Test\Page\Adminhtml\SearchIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Flow:
 * Preconditions:
 * 1. Products is created.
 *
 * Steps:
 * 1. Search products in frontend.
 * 2. Login to backend.
 * 3. Navigate to: Reports > Search Terms.
 * 4. Perform appropriate assertions.
 *
 * @group Search_Terms_(MX)
 * @ZephyrId MAGETWO-27106
 */
class SearchTermsReportEntityTest extends Injectable
{
    /**
     * Index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Search Index page.
     *
     * @var SearchIndex
     */
    protected $searchIndex;

    /**
     * FixtureFactory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Inject pages.
     *
     * @param CmsIndex $cmsIndex
     * @param SearchIndex $searchIndex
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(CmsIndex $cmsIndex, SearchIndex $searchIndex, FixtureFactory $fixtureFactory)
    {
        $this->cmsIndex = $cmsIndex;
        $this->searchIndex = $searchIndex;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Search Terms Report.
     *
     * @param string $product
     * @param int $countProducts
     * @param int $countSearch
     * @return array
     */
    public function test($product, $countProducts, $countSearch)
    {
        $this->markTestIncomplete('MAGETWO-30246');
        // Preconditions
        $productName = $this->createProducts($product, $countProducts);

        // Steps
        $this->cmsIndex->open();
        $this->searchProducts($productName, $countSearch);
        $this->searchIndex->open();

        return ['productName' => $productName];
    }

    /**
     * Create products.
     *
     * @param string $product
     * @param int $countProduct
     * @return string
     */
    protected function createProducts($product, $countProduct)
    {
        $name = 'simpleProductName' . mt_rand();
        for ($i = 0; $i < $countProduct; $i++) {
            $productFixture = $this->fixtureFactory->createByCode(
                'catalogProductSimple',
                ['dataSet' => $product, 'data' => ['name' => $name]]
            );
            $productFixture->persist();
        }
        return $name;
    }

    /**
     * Search products.
     *
     * @param string $productName
     * @param int $countSearch
     * @return void
     */
    protected function searchProducts($productName, $countSearch)
    {
        for ($i = 0; $i < $countSearch; $i++) {
            $this->cmsIndex->getSearchBlock()->search($productName);
        }
    }
}

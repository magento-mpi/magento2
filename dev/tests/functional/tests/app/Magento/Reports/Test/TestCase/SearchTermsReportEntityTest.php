<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Reports\Test\Page\Adminhtml\SearchIndex;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Test Creation for SearchTermsReportEntity
 *
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
     * Index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Search Index page
     *
     * @var SearchIndex
     */
    protected $searchIndex;

    /**
     * FixtureFactory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Inject pages
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
     * Search Terms Report
     *
     * @param CatalogProductSimple $product
     * @param int $countProducts
     * @param int $countSearch
     * @return array
     */
    public function test(CatalogProductSimple $product, $countProducts, $countSearch)
    {
        // Preconditions
        $productName = $this->createProducts($product, $countProducts);

        // Steps
        $this->cmsIndex->open();
        $this->searchProducts($productName, $countSearch);
        $this->searchIndex->open();

        return ['productName' => $productName];
    }

    /**
     * Create products
     *
     * @param CatalogProductSimple $product
     * @param int $countProduct
     * @return string
     */
    protected function createProducts(CatalogProductSimple $product, $countProduct)
    {
        for ($i = 0; $i < $countProduct; $i++) {
            $product->persist();
        }
        return $product->getName();
    }

    /**
     * Search products
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

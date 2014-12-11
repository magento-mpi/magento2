<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Flow:
 * Preconditions:
 * 1. All product types are created
 *
 * Steps:
 * 1. Navigate to frontend on index page
 * 2. Input test data into "search field" and press Enter key
 * 3. Perform all assertions
 *
 * @group Search_Frontend_(MX)
 * @ZephyrId MAGETWO-25095
 */
class SearchEntityResultsTest extends Injectable
{
    /**
     * CMS index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Inject data
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function __inject(CmsIndex $cmsIndex)
    {
        $this->cmsIndex = $cmsIndex;
    }

    /**
     * Run searching result test.
     *
     * @param string $products
     * @param CatalogSearchQuery $catalogSearch
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function test($products, CatalogSearchQuery $catalogSearch, FixtureFactory $fixtureFactory)
    {
        $result = [];
        $products = explode(',', $products);

        foreach ($products as $product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
            $result['products'][] = $product;
        }

        $this->cmsIndex->open();
        $this->cmsIndex->getSearchBlock()->search($catalogSearch->getQueryText());

        return $result;
    }
}

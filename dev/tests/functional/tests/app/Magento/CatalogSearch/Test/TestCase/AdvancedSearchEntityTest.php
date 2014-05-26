<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\CatalogSearch\Test\Page\Advanced;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Test Creation for AdvancedSearchEntity
 *
 * Test Flow:
 *
 * Preconditions:
 *
 * Two specific simple product is created(unique sku,name,short/full description, tax class)
 *
 * Steps:
 *
 * Navigate to Frontend
 * Click "Advanced Search"
 * Fill test data in to field(s)
 * Click "Search" button
 * Perform all asserts
 *
 * @group Search_Frontend_(MX)
 * @ZephyrId MAGETWO-24729
 */
class AdvancedSearchEntityTest extends Injectable
{
    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        /** @var CatalogProductSimple $productSymbols */
        $productSymbols = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'abc_dfj_simple_for_advancedsearch']
        );
        $productSymbols->persist();

        /** @var CatalogProductSimple $productNumbers */
        $productNumbers = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'adc_123_simple_for_advancedsearch']
        );
        $productNumbers->persist();

        return [
            'productsSearch' => [
                'simple_1' => $productSymbols,
                'simple_2' => $productNumbers
            ]
        ];
    }

    /**
     * Run test creation for advanced search entity
     *
     * @param array $products
     * @param CatalogProductSimple $productSearch
     * @param CmsIndex $cmsIndex
     * @param Advanced $searchPage
     * @return void
     */
    public function testSearch(
        array $products,
        CatalogProductSimple $productSearch,
        CmsIndex $cmsIndex,
        Advanced $searchPage
    ) {
        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->clickAdvancedSearchButton();
        $searchForm = $searchPage->getForm();
        $searchForm->fill($productSearch);
        $searchForm->submit();
    }
}

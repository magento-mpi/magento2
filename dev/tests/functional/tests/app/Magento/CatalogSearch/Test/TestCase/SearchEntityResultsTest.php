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
use Magento\GiftCard\Test\Fixture\GiftCardProduct;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductVirtual;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

/**
 * Test Creation for SearchEntity results
 *
 * Test Flow:
 *
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
     * Prepare test data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        /** @var GiftCardProduct $giftCardProduct */
        $giftCardProduct = $fixtureFactory->createByCode('giftCardProduct', ['dataSet' => 'customDefault']);
        $giftCardProduct->persist();

        /** @var CatalogProductSimple $simpleProduct */
        $simpleProduct = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'default']);
        $simpleProduct->persist();

        /** @var CatalogProductVirtual $virtualProduct */
        $virtualProduct = $fixtureFactory->createByCode('catalogProductVirtual', ['dataSet' => 'default']);
        $virtualProduct->persist();

        /** @var CatalogProductVirtual $virtualProduct */
        $configurableProduct = $fixtureFactory->createByCode(
            'catalogProductConfigurable',
            ['dataSet' => 'customDefault', 'persist' => true]
        );

        /** @var CatalogProductGrouped $groupedProduct */
        $groupedProduct = $fixtureFactory->createByCode('catalogProductGrouped', ['dataSet' => 'default']);
        $groupedProduct->persist();

        /** @var CatalogProductBundle $bundleDynamicProduct */
        $bundleDynamicProduct = $fixtureFactory->createByCode(
            'catalogProductBundle',
            [
                'dataSet' => 'bundle_dynamic_product',
                'data' => [
                    'bundle_selections' => [
                        'products' => 'catalogProductSimple::default'
                    ]
                ]
            ]
        );
        $bundleDynamicProduct->persist();

        /** @var CatalogProductBundle $bundleFixedProduct */
        $bundleFixedProduct = $fixtureFactory->createByCode(
            'catalogProductBundle',
            [
                'dataSet' => 'bundle_fixed_product',
                'data' => [
                    'bundle_selections' => [
                        'products' => 'catalogProductSimple::default'
                    ]
                ]
            ]
        );
        $bundleFixedProduct->persist();

        /** @var CatalogProductDownloadable $downloadableProduct */
        $downloadableProduct = $fixtureFactory->createByCode(
            'catalogProductDownloadable',
            ['dataSet' => 'default']
        );
        $downloadableProduct->persist();

        return [
            'products' => [
                'simple' => $simpleProduct,
                'virtual' => $virtualProduct,
                'giftcard' => $giftCardProduct,
                'downloadable' => $downloadableProduct,
                'grouped' => $groupedProduct,
                'configurable' => $configurableProduct,
                'bundle_dynamic' => $bundleDynamicProduct,
                'bundle_fixed' => $bundleFixedProduct
            ]
        ];
    }

    /**
     * Run suggest searching result test
     *
     * @param CatalogSearchQuery $catalogSearch
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function testSearch(CatalogSearchQuery $catalogSearch, CmsIndex $cmsIndex)
    {
        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->search($catalogSearch->getQueryText());
    }
}

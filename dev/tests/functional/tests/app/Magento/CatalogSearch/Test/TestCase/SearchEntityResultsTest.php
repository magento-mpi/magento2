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
     * Config to create products
     *
     * @var array
     */
    protected $products = [
        'giftCardProduct' => [
            'giftcard' => ['dataSet' => 'customDefault']
        ],
        'catalogProductSimple' => [
            'simple' => ['dataSet' => 'default']
        ],
        'catalogProductVirtual' => [
            'virtual' => ['dataSet' => 'default']
        ],
        'catalogProductConfigurable' => [
            'configurable' => ['dataSet' => 'customDefault', 'persist' => true]
        ],
        'catalogProductGrouped' => [
            'grouped' => ['dataSet' => 'default']
        ],
        'catalogProductBundle' => [
            'bundle_dynamic' => [
                'dataSet' => 'bundle_dynamic_product',
                'data' => [
                    'bundle_selections' => [
                        'products' => 'catalogProductSimple::default'
                    ]
                ]
            ],
            'bundle_fixed' => [
                'dataSet' => 'bundle_fixed_product',
                'data' => [
                    'bundle_selections' => [
                        'products' => 'catalogProductSimple::default'
                    ]
                ]
            ]
        ],
        'catalogProductDownloadable' => [
            'downloadable' => ['dataSet' => 'default']
        ]
    ];

    /**
     * Prepare test data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $products = [];
        foreach($this->products as $fixtureName => $productConfig) {
            foreach ($productConfig as $varName => $arguments) {
                $products[$varName] = $fixtureFactory->createByCode($fixtureName, $arguments);
                $products[$varName]->persist();
            }
        }

        return ['products' => $products];
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

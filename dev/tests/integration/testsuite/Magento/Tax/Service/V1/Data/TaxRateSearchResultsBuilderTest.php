<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Integration test for \Magento\Tax\Service\V1\Data\TaxRateSearchResultsBuilder
 */
class TaxRateSearchResultsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * TaxRateSearchResults builder
     *
     * @var TaxRateSearchResultsBuilder
     */
    private $builder;

    /**
     * TaxRate builder
     *
     * @var TaxRateBuilder
     */
    private $taxRateBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->builder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxRateSearchResultsBuilder');
        $this->taxRateBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxRateBuilder');
    }

    /**
     * @param array $dataArray
     * @dataProvider createDataProvider
     */
    public function testCreateWithPopulateWithArray($dataArray)
    {
        $taxRateSearchResults = $this->builder->populateWithArray($dataArray)->create();
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRateSearchResults', $taxRateSearchResults);
        $this->assertEquals($dataArray, $taxRateSearchResults->__toArray());
    }

    public function createDataProvider()
    {
        list($rateA, $rateB) = $this->getRateData();

        return [
            [[]],
            [
                [
                    TaxRateSearchResults::KEY_SEARCH_CRITERIA => [
                        SearchCriteria::CURRENT_PAGE => 1,
                        SearchCriteria::PAGE_SIZE => 2,
                        SearchCriteria::SORT_ORDERS => SearchCriteria::SORT_DESC,
                        SearchCriteria::FILTER_GROUPS => [],
                    ],
                    TaxRateSearchResults::KEY_TOTAL_COUNT => 2,
                    TaxRateSearchResults::KEY_ITEMS => [
                        $rateA,
                        $rateB
                    ],
                ]
            ]
        ];
    }

    /**
     * @param array $dataArray
     * @dataProvider createDataProvider
     */
    public function testPopulate($dataArray)
    {
        $taxRateSearchResultsFromArray = $this->builder->populateWithArray($dataArray)->create();
        $taxRateSearchResults = $this->builder->populate($taxRateSearchResultsFromArray)->create();
        $this->assertEquals($taxRateSearchResultsFromArray, $taxRateSearchResults);
    }

    /**
     * @dataProvider mergeDataProvider
     */
    public function testMergeDataObjects($firstDataSet, $secondDataSet, $mergedData)
    {
        $taxRateSearchResults = $this->builder->populateWithArray($mergedData)->create();
        $taxRateSearchResults1 = $this->builder->populateWithArray($firstDataSet)->create();
        $taxRateSearchResults2 = $this->builder->populateWithArray($secondDataSet)->create();
        $taxRateSearchResultsMerged = $this->builder->mergeDataObjects($taxRateSearchResults1, $taxRateSearchResults2);
        $this->assertEquals($taxRateSearchResults->__toArray(), $taxRateSearchResultsMerged->__toArray());
    }

    /**
     * @dataProvider mergeDataProvider
     */
    public function testMergeDataObjectWithArray($firstDataSet, $secondDataSet, $mergedData)
    {
        $taxRateSearchResults = $this->builder->populateWithArray($mergedData)->create();
        $taxRateSearchResults1 = $this->builder->populateWithArray($firstDataSet)->create();
        $taxRateSearchResultsMerged = $this->builder->mergeDataObjectWithArray($taxRateSearchResults1, $secondDataSet);
        $this->assertEquals($taxRateSearchResults->__toArray(), $taxRateSearchResultsMerged->__toArray());
    }

    public function mergeDataProvider()
    {
        list($rateA, $rateB) = $this->getRateData();

        return
            [
                'basicMerge' => [
                    'firstDataSet' => [
                        TaxRateSearchResults::KEY_SEARCH_CRITERIA => [
                            SearchCriteria::CURRENT_PAGE => 1,
                            SearchCriteria::PAGE_SIZE => 2,
                            SearchCriteria::SORT_ORDERS => SearchCriteria::SORT_DESC,
                            SearchCriteria::FILTER_GROUPS => [],
                        ],
                        TaxRateSearchResults::KEY_ITEMS => [
                            $rateA
                        ],
                    ],
                    'SecondDataSet' => [
                        TaxRateSearchResults::KEY_SEARCH_CRITERIA => [
                            SearchCriteria::CURRENT_PAGE => 1,
                            SearchCriteria::PAGE_SIZE => 2,
                            SearchCriteria::SORT_ORDERS => SearchCriteria::SORT_DESC,
                            SearchCriteria::FILTER_GROUPS => [],
                        ],
                        TaxRateSearchResults::KEY_TOTAL_COUNT => 2,
                        TaxRateSearchResults::KEY_ITEMS => [
                            $rateB
                        ],
                    ],
                    'mergedData' => [
                        TaxRateSearchResults::KEY_SEARCH_CRITERIA => [
                            SearchCriteria::CURRENT_PAGE => 1,
                            SearchCriteria::PAGE_SIZE => 2,
                            SearchCriteria::SORT_ORDERS => SearchCriteria::SORT_DESC,
                            SearchCriteria::FILTER_GROUPS => [],
                        ],
                        TaxRateSearchResults::KEY_TOTAL_COUNT => 2,
                        TaxRateSearchResults::KEY_ITEMS => [
                            $rateB
                        ],
                    ]
                ]
            ];
    }

    private function getRateData()
    {
        $rateA = [
            TaxRate::KEY_ID => 1,
            TaxRate::KEY_COUNTRY_ID => 'US',
            TaxRate::KEY_REGION_ID => '8',
            TaxRate::KEY_PERCENTAGE_RATE => '8.25',
            TaxRate::KEY_CODE => 'US-CA-*-Rate 1',
            TaxRate::KEY_POSTCODE => '78728'
        ];

        $rateB = [
            TaxRate::KEY_ID => 1,
            TaxRate::KEY_ZIP_RANGE => ['from' => 78701, 'to' => 78780],
        ];
        return ([$rateA, $rateB]);
    }
}

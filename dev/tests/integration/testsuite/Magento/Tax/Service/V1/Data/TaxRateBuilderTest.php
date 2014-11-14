<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

/**
 * Integration test for \Magento\Tax\Service\V1\Data\TaxRateBuilder
 */
class TaxRateBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * TaxRate builder
     *
     * @var TaxRateBuilder
     */
    private $builder;

    /**
     * ZipRange builder
     *
     * @var ZipRangeBuilder
     */
    private $zipRangeBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->builder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxRateBuilder');
        $this->zipRangeBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\ZipRangeBuilder');
    }

    /**
     * @param array $dataArray
     * @param array $zipRangeArray
     * @dataProvider createDataProvider
     */
    public function testCreateWithPopulateWithArray($dataArray, $zipRangeArray = [])
    {
        if (!empty($zipRangeArray)) {
            $dataArray[TaxRate::KEY_ZIP_RANGE] = $zipRangeArray;
        }
        $taxRate = $this->builder->populateWithArray($dataArray)->create();
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRate', $taxRate);
        $this->assertEquals($dataArray, $taxRate->__toArray());
    }

    /**
     * @param array $dataArray
     * @param array $zipRangeArray
     * @dataProvider createDataProvider
     */
    public function testPopulate($dataArray, $zipRangeArray = [])
    {
        if (!empty($zipRangeArray)) {
            $dataArray[TaxRate::KEY_ZIP_RANGE] = $zipRangeArray;
        }
        $taxRateFromArray = $this->builder->populateWithArray($dataArray)->create();
        $taxRate = $this->builder->populate($taxRateFromArray)->create();
        $this->assertEquals($taxRateFromArray, $taxRate);
    }

    public function createDataProvider()
    {

        $data = [
            'id' => 1,
            'country_id' => 'US',
            'region_id' => '8',
            'postcode' => '78729',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
        ];

        return [
            'just data'             => [$data],
            'just empty data'       => [[]],
            'data and ziprange'     => [$data, ['from' => 78701, 'to' => 78780]],
            'data and just from'    => [$data, ['from' => 78701]],
            'no data and ziprange'  => [[], ['from' => 78701, 'to' => 78780]],
            'no data and just from' => [[], ['from' => 78701]],
        ];
    }

    /**
     * @dataProvider mergeDataProvider
     */
    public function testMergeDataObjects($firstRateArray, $secondRateArray, $expectedResultsArray)
    {
        $expectedTaxRate = $this->builder->populateWithArray($expectedResultsArray)->create();
        $taxRate1 = $this->builder->populateWithArray($firstRateArray)->create();
        $taxRate2 = $this->builder->populateWithArray($secondRateArray)->create();
        $taxRateMerged = $this->builder->mergeDataObjects($taxRate1, $taxRate2)
            ->create();
        $this->assertEquals($expectedTaxRate->__toArray(), $taxRateMerged->__toArray());
    }

    /**
     * @dataProvider mergeDataProvider
     */
    public function testMergeDataObjectWithArray($firstRateArray, $secondRateArray, $expectedResultsArray)
    {

        $taxRate = $this->builder->populateWithArray($expectedResultsArray)->create();
        $taxRate1 = $this->builder->populateWithArray($firstRateArray)->create();
        $taxRateMerged = $this->builder->mergeDataObjectWithArray($taxRate1, $secondRateArray)
            ->create();
        $this->assertEquals($taxRate->__toArray(), $taxRateMerged->__toArray());
    }

    public function mergeDataProvider()
    {
        return [
            'basicMerge' => [
                'postcode' => [
                    'id' => 1,
                    'country_id' => 'US',
                    'region_id' => 7,
                    'postcode' => '78729',
                    'percentage_rate' => 8.25,
                    'code' => 'US-CA-*-Rate 1',
                ],
                'ziprange' => [
                    'id' => 1,
                    'country_id' => 'US',
                    'percentage_rate' => 8.00,
                    'code' => 'US-CA-*-Rate 1',
                    'zip_range' => ['from' => 78701, 'to' => 78780]
                ],
                'merged' => [
                    'id' => 1,
                    'country_id' => 'US',
                    'region_id' => 7,
                    'postcode' => '78729',
                    'percentage_rate' => 8.0,
                    'code' => 'US-CA-*-Rate 1',
                    'zip_range' => ['from' => 78701, 'to' => 78780]
                ],
            ],
        ];
    }
}

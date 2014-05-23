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
        $taxRate2 = $this->generateTaxRateWithSetters($dataArray);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRate', $taxRate);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRate', $taxRate2);
        $this->assertEquals($taxRate2, $taxRate);
        $this->assertEquals($dataArray, $taxRate->__toArray());
        $this->assertEquals($dataArray, $taxRate2->__toArray());
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
            [$data],
            [[]],
            [$data, ['from' => 78701, 'to' => 78780]],
            [$data, ['from' => 78701]],
            [[], ['from' => 78701, 'to' => 78780]],
            [[], ['from' => 78701]],
        ];
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
        $taxRate = $this->generateTaxRateWithSetters($dataArray);
        $taxRate2 = $this->builder->populate($taxRate)->create();
        $this->assertEquals($taxRate, $taxRate2);
    }

    public function testMergeDataObjects()
    {
        $data1 = [
            'id' => 1,
            'country_id' => 'US',
            'region_id' => '8',
            'postcode' => '78729',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
        ];

        $data2 = [
            'id' => 1,
            'country_id' => 'US',
            'postcode' => '78727',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
            'zip_range' => ['from' => 78701, 'to' => 78780]
        ];

        $dataMerged = [
            'id' => 1,
            'country_id' => 'US',
            'region_id' => 8,
            'postcode' => '78727',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
            'zip_range' => ['from' => 78701, 'to' => 78780]
        ];

        $taxRate = $this->builder->populateWithArray($dataMerged)->create();
        $taxRate1 = $this->builder->populateWithArray($data1)->create();
        $taxRate2 = $this->builder->populateWithArray($data2)->create();
        $taxRateMerged = $this->builder->mergeDataObjects($taxRate1, $taxRate2);
        $this->assertEquals($taxRate->__toArray(), $taxRateMerged->__toArray());
    }

    public function testMergeDataObjectWithArray()
    {
        $data1 = [
            'id' => 1,
            'country_id' => 'US',
            'region_id' => '8',
            'postcode' => '78729',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
        ];

        $data2 = [
            'id' => 1,
            'country_id' => 'US',
            'postcode' => '78727',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
            'zip_range' => ['from' => 78701, 'to' => 78780]
        ];

        $dataMerged = [
            'id' => 1,
            'country_id' => 'US',
            'region_id' => 8,
            'postcode' => '78727',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate 1',
            'zip_range' => ['from' => 78701, 'to' => 78780]
        ];

        $taxRate = $this->builder->populateWithArray($dataMerged)->create();
        $taxRate1 = $this->builder->populateWithArray($data1)->create();
        $taxRateMerged = $this->builder->mergeDataObjectWithArray($taxRate1, $data2);
        $this->assertEquals($taxRate->__toArray(), $taxRateMerged->__toArray());
    }

    /**
     * @param array $dataArray
     * @return TaxRate
     */
    protected function generateTaxRateWithSetters($dataArray)
    {
        $this->builder->populateWithArray([]);
        if (array_key_exists(TaxRate::KEY_ID, $dataArray)) {
            $this->builder->setId($dataArray[TaxRate::KEY_ID]);
        }
        if (array_key_exists(TaxRate::KEY_COUNTRY_ID, $dataArray)) {
            $this->builder->setCountryId($dataArray[TaxRate::KEY_COUNTRY_ID]);
        }
        if (array_key_exists(TaxRate::KEY_REGION_ID, $dataArray)) {
            $this->builder->setRegionId($dataArray[TaxRate::KEY_REGION_ID]);
        }
        if (array_key_exists(TaxRate::KEY_POSTCODE, $dataArray)) {
            $this->builder->setPostcode($dataArray[TaxRate::KEY_POSTCODE]);
        }
        if (array_key_exists(TaxRate::KEY_PERCENTAGE_RATE, $dataArray)) {
            $this->builder->setPercentageRate($dataArray[TaxRate::KEY_PERCENTAGE_RATE]);
        }
        if (array_key_exists(TaxRate::KEY_CODE, $dataArray)) {
            $this->builder->setCode($dataArray[TaxRate::KEY_CODE]);
        }
        if (array_key_exists(TaxRate::KEY_ZIP_RANGE, $dataArray)) {
            $this->builder->setZipRange(
                $this->zipRangeBuilder->populateWithArray($dataArray[TaxRate::KEY_ZIP_RANGE])
                    ->create()
            );
        }
        $taxRate2 = $this->builder->create();
        return $taxRate2;
    }
}

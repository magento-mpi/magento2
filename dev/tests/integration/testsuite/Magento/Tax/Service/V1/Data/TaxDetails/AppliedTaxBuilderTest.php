<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\TaxDetails;

/**
 * Integration test for \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder
 */
class AppliedTaxBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * AppliedTax builder
     *
     * @var AppliedTaxBuilder
     */
    private $builder;

    /**
     * AppliedTaxRate builder
     *
     * @var AppliedTaxRateBuilder
     */
    private $appliedTaxRateBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->builder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder');
        $this->appliedTaxRateBuilder = $this->objectManager->create(
            'Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxRateBuilder'
        );
    }

    /**
     * @param array $dataArray
     * @param array $appliedTaxRatesArray
     * @dataProvider createDataProvider
     */
    public function testCreateWithPopulateWithArray($dataArray, $appliedTaxRatesArray = [])
    {
        if (!empty($appliedTaxRatesArray)) {
            $dataArray[AppliedTax::KEY_RATES] = $appliedTaxRatesArray;
        }
        $appliedTax = $this->builder->populateWithArray($dataArray)->create();
        $appliedTax2 = $this->generateDataObjectWithSetters($dataArray);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax', $appliedTax);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax', $appliedTax2);
        $this->assertEquals($appliedTax2, $appliedTax);
        $this->assertEquals($dataArray, $appliedTax->__toArray());
        $this->assertEquals($dataArray, $appliedTax2->__toArray());
    }

    public function createDataProvider()
    {
        $data = [
            'tax_rate_key' => 'key',
            'percent' => '8.25',
            'amount' => '8',
        ];

        $ratesData = [
            [
                'code' => 'rate1',
                'title' => 'rate1Title',
                'percent' => '8.25',
            ],
            [
                'code' => 'rate2',
                'title' => 'rate2Title',
                'percent' => '7.25',
            ],
        ];
        return [
            'no_data' => [[]],
            'no_rates' => [$data],
            'data_n_rates' => [$data, $ratesData],
            'rates_only' => [[], $ratesData],
        ];
    }

    /**
     * @param array $dataArray
     * @param array $appliedTaxRatesArray
     * @dataProvider createDataProvider
     */
    public function testPopulate($dataArray, $appliedTaxRatesArray = [])
    {
        if (!empty($appliedTaxRatesArray)) {
            $dataArray[AppliedTax::KEY_RATES] = $appliedTaxRatesArray;
        }
        $appliedTax = $this->generateDataObjectWithSetters($dataArray);
        $appliedTax2 = $this->builder->populate($appliedTax)->create();
        $this->assertEquals($appliedTax, $appliedTax2);
    }

    public function testMergeDataObjects()
    {
        $data1 = [
            'tax_rate_key' => 'key',
            'percent' => '8.25',
            'amount' => '8',
            'rates' => [
                [
                    'code' => 'rate1',
                    'title' => 'rate1Title',
                    'percent' => '8.25',
                ],
            ],
        ];

        $data2 = [
            'tax_rate_key' => 'key2',
            'percent' => '8.25',
            'amount' => '8',
            'rates' => [
                [
                    'code' => 'rate2',
                    'title' => 'rate2Title',
                    'percent' => '8.25',
                ],
            ],
        ];

        $dataMerged = [
            'tax_rate_key' => 'key2',
            'percent' => '8.25',
            'amount' => '8',
            'rates' => [
                [
                    'code' => 'rate2',
                    'title' => 'rate2Title',
                    'percent' => '8.25',
                ],
            ],
        ];

        $appliedTax = $this->builder->populateWithArray($dataMerged)->create();
        $appliedTax1 = $this->builder->populateWithArray($data1)->create();
        $appliedTax2 = $this->builder->populateWithArray($data2)->create();
        $appliedTaxMerged = $this->builder->mergeDataObjects($appliedTax1, $appliedTax2);
        $this->assertEquals($appliedTax->__toArray(), $appliedTaxMerged->__toArray());
    }

    public function testMergeDataObjectWithArray()
    {
        $data1 = [
            'tax_rate_key' => 'key',
            'percent' => '8.25',
            'amount' => '8',
            'rates' => [
                [
                    'code' => 'rate1',
                    'title' => 'rate1Title',
                    'percent' => '8.25',
                ],
            ],
        ];

        $data2 = [
            'tax_rate_key' => 'key2',
            'percent' => '8.25',
            'amount' => '8',
            'rates' => [
                [
                    'code' => 'rate2',
                    'title' => 'rate2Title',
                    'percent' => '8.25',
                ],
            ],
        ];

        $dataMerged = [
            'tax_rate_key' => 'key2',
            'percent' => '8.25',
            'amount' => '8',
            'rates' => [
                [
                    'code' => 'rate2',
                    'title' => 'rate2Title',
                    'percent' => '8.25',
                ],
            ],
        ];

        $appliedTax = $this->builder->populateWithArray($dataMerged)->create();
        $appliedTax1 = $this->builder->populateWithArray($data1)->create();
        $appliedTaxMerged = $this->builder->mergeDataObjectWithArray($appliedTax1, $data2);
        $this->assertEquals($appliedTax->__toArray(), $appliedTaxMerged->__toArray());
    }

    /**
     * @param array $dataArray
     * @return AppliedTax
     */
    protected function generateDataObjectWithSetters($dataArray)
    {
        $this->builder->populateWithArray([]);
        if (array_key_exists(AppliedTax::KEY_TAX_RATE_KEY, $dataArray)) {
            $this->builder->setTaxRateKey($dataArray[AppliedTax::KEY_TAX_RATE_KEY]);
        }
        if (array_key_exists(AppliedTax::KEY_PERCENT, $dataArray)) {
            $this->builder->setPercent($dataArray[AppliedTax::KEY_PERCENT]);
        }
        if (array_key_exists(AppliedTax::KEY_AMOUNT, $dataArray)) {
            $this->builder->setAmount($dataArray[AppliedTax::KEY_AMOUNT]);
        }
        if (array_key_exists(AppliedTax::KEY_RATES, $dataArray)) {
            $rates = [];
            foreach ($dataArray[AppliedTax::KEY_RATES] as $rateArray) {
                $rates[] = $this->appliedTaxRateBuilder->populateWithArray($rateArray)
                    ->create();
            }
            $this->builder->setRates($rates);
        }
        return $this->builder->create();
    }
}

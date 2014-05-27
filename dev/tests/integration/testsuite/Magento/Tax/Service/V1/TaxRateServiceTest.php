<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\TestFramework\Helper\Bootstrap;

class TaxRateServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Tax\Service\V1\Data\TaxRateBuilder
     */
    private $taxRateBuilder;

    /**
     * TaxRateService
     *
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface
     */
    private $taxRateService;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->taxRateService = $this->objectManager->get('Magento\Tax\Service\V1\TaxRateServiceInterface');
        $this->taxRateBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxRateBuilder');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateTaxRate()
    {
        $taxData = [
            'country_id' => 'US',
            'region_id' => '8',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_range' => ['from' => 78765, 'to' => 78780]
        ];
        // Tax rate data object created
        $taxRate = $this->taxRateBuilder->populateWithArray($taxData)->create();
        //Tax rate service call
        $taxRateServiceData = $this->taxRateService->createTaxRate($taxRate);

        //Assertions
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRate', $taxRateServiceData);
        $this->assertEquals($taxData['country_id'], $taxRateServiceData->getCountryId());
        $this->assertEquals($taxData['region_id'], $taxRateServiceData->getRegionId());
        $this->assertEquals($taxData['percentage_rate'], $taxRateServiceData->getPercentageRate());
        $this->assertEquals($taxData['code'], $taxRateServiceData->getCode());
        $this->assertEquals($taxData['region_id'], $taxRateServiceData->getRegionId());
        $this->assertEquals($taxData['percentage_rate'], $taxRateServiceData->getPercentageRate());
        $this->assertEquals($taxData['zip_range']['from'], $taxRateServiceData->getZipRange()->getFrom());
        $this->assertEquals($taxData['zip_range']['to'], $taxRateServiceData->getZipRange()->getTo());
        $this->assertNotNull($taxRateServiceData->getId());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Code already exists.
     * @magentoDbIsolation enabled
     */
    public function testCreateTaxRateDuplicateCodes()
    {
        $invalidTaxData = [
            'country_id' => 'US',
            'region_id' => '8',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_range' => ['from' => 78765, 'to' => 78780]
        ];
        $taxRate = $this->taxRateBuilder->populateWithArray($invalidTaxData)->create();
        //Service call initiated twice to add the same code
        $this->taxRateService->createTaxRate($taxRate);
        $this->taxRateService->createTaxRate($taxRate);
    }

    /**
     * @param array $dataArray
     * @param string $errorMessages
     * @dataProvider createDataProvider
     * @throws \Magento\Framework\Exception\InputException
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateTaxRateWithExceptionMessages($dataArray, $errorMessages)
    {
        $expectedErrorMessages = [
            'country_id is a required field.',
            'region_id is a required field.',
            'percentage_rate is a required field.',
            'code is a required field.'
        ];
        $expectedErrorMessages = array_merge($expectedErrorMessages, $errorMessages);
        $taxRate = $this->taxRateBuilder->populateWithArray($dataArray)->create();
        try {
            $this->taxRateService->createTaxRate($taxRate);
        } catch (InputException $exception) {
            $errors = $exception->getErrors();
            foreach ($errors as $key => $error) {
                $this->assertEquals($expectedErrorMessages[$key], $error->getMessage());
            }
            throw $exception;
        }
    }

    public function testGetTaxRates()
    {
        $taxRates = $this->taxRateService->getTaxRates();
        $this->assertEquals(2, count($taxRates));
        foreach($taxRates as $taxRate) {
            if ($taxRate->getId() == 1) {
                $this->assertEquals('US', $taxRate->getCountryId());
                $this->assertEquals(12, $taxRate->getRegionId());
                $this->assertEquals(8.2500, $taxRate->getPercentageRate());
                $this->assertNull($taxRate->getZipRange());
            }

            if ($taxRate->getId() == 2) {
                $this->assertEquals('US', $taxRate->getCountryId());
                $this->assertEquals(43, $taxRate->getRegionId());
                $this->assertEquals(8.3750, $taxRate->getPercentageRate());
                $this->assertNull($taxRate->getZipRange());
            }
        }
    }

    public function createDataProvider()
    {
        $errorMessages = [
            [
                'Invalid value of "from" provided for the zip_from field.',
                'Invalid value of "to" provided for the zip_to field.'
            ],
            ['postcode is a required field.']
        ];
        return [
            ['invalidZipRange' => ['zip_range' => ['from' => 'from', 'to' => 'to']], 'error' => $errorMessages[0]],
            ['emptyZipRange' => ['zip_range' => ['from' => '', 'to' => '']], 'error' => $errorMessages[1]],
            [
                'zipRangeAndPostCode' => ['postcode' => 78727, 'zip_range' => ['from' => 78765, 'to' => 78780]],
                'error' => []
            ]
        ];
    }
}

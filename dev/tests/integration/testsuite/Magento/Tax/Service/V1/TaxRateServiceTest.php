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

    public function testCreateTaxRate()
    {
        $taxData = [
            'country_id' => 'US',
            'region_id' => '8',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate'.rand(),
            'zip_range' => ['from' => 78765, 'to' => 78780]
        ];
        $taxRate = $this->taxRateBuilder->populateWithArray($taxData)->create();
        $taxRateServiceData = $this->taxRateService->createTaxRate($taxRate);
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
     * Test create tax rate with invalid data
     *
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateTaxRateWithException()
    {
        $invalidTaxData = [
            'country_id' => 'US',
            'region_id' => '8',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_range' => ['from' => '', 'to' => 78780]
        ];
        $taxRate = $this->taxRateBuilder->populateWithArray($invalidTaxData)->create();
        $this->taxRateService->createTaxRate($taxRate);
    }

    /**
     * Test create tax rate existing code
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Code already exists.
     */
    public function testCreateTaxRateWithModelException()
    {
        $taxRates = $this->taxRateService->getTaxRates();
        $invalidTaxData = [
            'country_id' => 'US',
            'region_id' => '8',
            'percentage_rate' => '8.25',
            'code' => $taxRates[0]->getCode(),
            'zip_range' => ['from' => 78765, 'to' => 78780]
        ];
        $taxRate = $this->taxRateBuilder->populateWithArray($invalidTaxData)->create();
        $this->taxRateService->createTaxRate($taxRate);
    }


    public function testCreateTaxRateWithExceptionMessages()
    {
        $invalidTaxData = [
            'zip_range' => ['from' => 'from', 'to' => 'to']
        ];
        $expectedErrorMessages = [
            'country_id is a required field.',
            'region_id is a required field.',
            'percentage_rate is a required field.',
            'code is a required field.',
            'Invalid value of "from" provided for the zip_from field.',
            'Invalid value of "to" provided for the zip_to field.'
        ];
        $taxRate = $this->taxRateBuilder->populateWithArray($invalidTaxData)->create();
        try {
            $this->taxRateService->createTaxRate($taxRate);
        } catch (InputException $exception) {
            $errors = $exception->getErrors();
            foreach ($errors as $key => $error) {
                $this->assertEquals($expectedErrorMessages[$key], $error->getMessage());
            }
        }
    }
}

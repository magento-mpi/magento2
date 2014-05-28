<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Tax\Service\V1\Data\ZipRangeBuilder;
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
        $this->assertEquals('78765-78780', $taxRateServiceData->getPostcode());
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

    public function createDataProvider()
    {
        return [
            'invalidZipRange' => [
                ['zip_range' => ['from' => 'from', 'to' => 'to']],
                'error' => [
                    'Invalid value of "from" provided for the zip_from field.',
                    'Invalid value of "to" provided for the zip_to field.'
                ]
            ],
            'emptyZipRange' => [
                ['zip_range' => ['from' => '', 'to' => '']],
                'error' => [
                    'Invalid value of "" provided for the zip_from field.',
                    'Invalid value of "" provided for the zip_to field.'
                ]
            ],
            'empty' => [
                [],
                'error' => ['postcode is a required field.']
            ],
            'zipRangeAndPostcode' => [
                ['postcode' => 78727, 'zip_range' => ['from' => 78765, 'to' => 78780]],
                'error' => []
            ]
        ];
    }

    public function testGetTaxRates()
    {
        $taxRates = $this->taxRateService->getTaxRates();
        $this->assertEquals(2, count($taxRates));
        foreach ($taxRates as $taxRate) {
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

    /**
     * @magentoDbIsolation enabled
     */
    public function testUpdateTaxRates()
    {
        /** @var ZipRangeBuilder $zipRangeBuilder */
        $zipRangeBuilder = $this->objectManager->get('Magento\Tax\Service\V1\Data\ZipRangeBuilder');
        $taxRate = $this->taxRateBuilder
            ->setCountryId('US')
            ->setRegionId(42)
            ->setPercentageRate(8.25)
            ->setCode('UpdateTaxRates')
            ->setPostcode('78780')
            ->create();
        $taxRate = $this->taxRateService->createTaxRate($taxRate);
        $zipRange = $zipRangeBuilder->setFrom(78700)->setTo(78780)->create();
        $updatedTaxRate = $this->taxRateBuilder->populate($taxRate)
            ->setPostcode(null)
            ->setZipRange($zipRange)
            ->create();

        $this->taxRateService->updateTaxRate($updatedTaxRate);

        // Ideally call getTaxRate($taxRate->getId()) here and verify contents reflect the updated version
        $retrievedRate = $this->getTaxRate($taxRate->getId());
        // Expect the service to have filled in the new postcode for us
        $updatedTaxRate = $this->taxRateBuilder->populate($updatedTaxRate)->setPostcode('78700-78780')->create();
        $this->assertEquals($retrievedRate->__toArray(), $updatedTaxRate->__toArray());
        $this->assertNotEquals($retrievedRate->__toArray(), $taxRate->__toArray());
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage taxRateId =
     */
    public function testUpdateTaxRateNoId()
    {
        $taxRate = $this->taxRateBuilder
            ->setCountryId('US')
            ->setRegionId(42)
            ->setPercentageRate(8.25)
            ->setCode('UpdateTaxRates')
            ->setPostcode('78780')
            ->create();

        $this->taxRateService->updateTaxRate($taxRate);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage postcode
     */
    public function testUpdateTaxRateMissingRequiredFields()
    {
        $taxRate = $this->taxRateBuilder
            ->setCountryId('US')
            ->setRegionId(42)
            ->setPercentageRate(8.25)
            ->setCode('UpdateTaxRates')
            ->setPostcode('78780')
            ->create();
        $taxRate = $this->taxRateService->createTaxRate($taxRate);
        $updatedTaxRate = $this->taxRateBuilder->populate($taxRate)
            ->setPostcode(null)
            ->create();

        $this->taxRateService->updateTaxRate($updatedTaxRate);
    }

    /**
     * Helper function to get a specific tax rate
     *
     * @param int $id
     * @return Data\TaxRate|null
     */
    private function getTaxRate($id)
    {
        $rates = $this->taxRateService->getTaxRates();
        foreach ($rates as $rate) {
            if ($rate->getId() === $id) {
                return $rate;
            }
        }
        return null;
    }
}

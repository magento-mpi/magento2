<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class TaxRateServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "taxTaxRateServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/taxRate";

    /** @var \Magento\Tax\Model\Calculation\Rate[] */
    private $fixtureTaxRates;

    /** @var \Magento\Tax\Model\ClassModel[] */
    private $fixtureTaxClasses;

    /** @var \Magento\Tax\Model\Calculation\Rule[] */
    private $fixtureTaxRules;

    /**
     * @var TaxRateServiceInterface
     */
    private $taxRateService;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->taxRateService = $objectManager->get('Magento\Tax\Service\V1\TaxRateService');
        /** Initialize tax classes, tax rates and tax rules defined in fixture Magento/Tax/_files/tax_classes.php */
        $this->getFixtureTaxRates();
        $this->getFixtureTaxClasses();
        $this->getFixtureTaxRules();
    }

    public function tearDown()
    {
        $taxRules = $this->getFixtureTaxRules();
        if (count($taxRules)) {
            $taxRates = $this->getFixtureTaxRates();
            $taxClasses = $this->getFixtureTaxClasses();
            foreach ($taxRules as $taxRule) {
                $taxRule->delete();
            }
            foreach ($taxRates as $taxRate) {
                $taxRate->delete();
            }
            foreach ($taxClasses as $taxClass) {
                $taxClass->delete();
            }
        }
    }

    public function testCreateTaxRateExistingCode()
    {
        $data = [
            'tax_rate' => [
                'country_id' => 'US',
                'region_id' => 12,
                'postcode' => '*',
                'code' => 'US-CA-*-Rate 1',
                'percentage_rate' => '8.2501'
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateTaxRate'
            ]
        ];
        try {
            $this->_webApiCall($serviceInfo, $data);
            $this->fail('Expected exception was not raised');
        } catch (\Exception $e) {
            $expectedMessage = 'Code already exists.';

            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    public function testCreateTaxRate()
    {
        $data = [
            'tax_rate' => [
                'country_id' => 'US',
                'region_id' => 12,
                'postcode' => '*',
                'code' => 'Test Tax Rate ' . microtime(),
                'percentage_rate' => '8.2501'
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateTaxRate'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $data);
        $this->assertArrayHasKey('id', $result);
        $taxRateId = $result['id'];
        /** Ensure that tax rate was actually created in DB */
        /** @var \Magento\Tax\Model\Calculation\Rate $taxRate */
        $taxRate = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rate');
        $this->assertEquals($taxRateId, $taxRate->load($taxRateId)->getId(), 'Tax rate was not created in  DB.');
        $taxRate->delete();
    }

    /**
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testUpdateTaxRate()
    {
        $fixtureRate = $this->getFixtureTaxRates()[0];

        $data = [
            'tax_rate' => [
                'id' => $fixtureRate->getId(),
                'region_id' => 43,
                'country_id' => 'US',
                'postcode' => '07400',
                'code' => 'Test Tax Rate ' . microtime(),
                'percentage_rate' => 3.456
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateTaxRate'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $data);
        $this->assertTrue($result);
        $expectedRateData = $data['tax_rate'];
        /** Ensure that tax rate was actually updated in DB */
        /** @var \Magento\Tax\Model\Calculation\Rate $taxRate */
        $taxRate = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rate');
        $taxRateModel = $taxRate->load($fixtureRate->getId());
        $this->assertEquals($expectedRateData['id'], $taxRateModel->getId(), 'Tax rate was not updated in  DB.');
        $this->assertEquals(
            $expectedRateData['region_id'],
            $taxRateModel->getTaxRegionId(),
            'Tax rate was not updated in  DB.'
        );
        $this->assertEquals(
            $expectedRateData['country_id'],
            $taxRateModel->getTaxCountryId(),
            'Tax rate was not updated in  DB.'
        );
        $this->assertEquals(
            $expectedRateData['postcode'],
            $taxRateModel->getTaxPostcode(),
            'Tax rate was not updated in  DB.'
        );
        $this->assertEquals($expectedRateData['code'], $taxRateModel->getCode(), 'Tax rate was not updated in  DB.');
        $this->assertEquals(
            $expectedRateData['percentage_rate'],
            $taxRateModel->getRate(),
            'Tax rate was not updated in  DB.'
        );
    }

    public function testUpdateTaxRateNotExisting()
    {
        $data = [
            'tax_rate' => [
                'id' => 555,
                'region_id' => 43,
                'country_id' => 'US',
                'postcode' => '07400',
                'code' => 'Test Tax Rate ' . microtime(),
                'percentage_rate' => 3.456
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateTaxRate'
            ]
        ];
        try {
            $this->_webApiCall($serviceInfo, $data);
            $this->fail('Expected exception was not raised');
        } catch (\Exception $e) {
            $expectedMessage = 'No such entity with %fieldName = %fieldValue';

            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    public function testGetTaxRate()
    {
        $taxRateId = 2;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRateId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetTaxRate'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, ['rateId' => $taxRateId]);
        $expectedRateData = [
            'id' => 2,
            'country_id' => 'US',
            'region_id' => 43,
            'postcode' => '*',
            'code' => 'US-NY-*-Rate 1',
            'percentage_rate' => '8.3750'
        ];
        $this->assertEquals($expectedRateData, $result);
    }

    public function testGetTaxRateNotExist()
    {
        $taxRateId = 37865;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRateId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetTaxRate'
            ]
        ];
        try {
            $this->_webApiCall($serviceInfo, ['rateId' => $taxRateId]);
            $this->fail('Expected exception was not raised');
        } catch (\Exception $e) {
            $expectedMessage = 'No such entity with %fieldName = %fieldValue';

            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }

    }

    /**
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testDeleteTaxRate()
    {
        /** Tax rules must be deleted since tax rate cannot be deleted if there are any tax rules associated with it */
        $taxRules = $this->getFixtureTaxRules();
        foreach ($taxRules as $taxRule) {
            $taxRule->delete();
        }

        $fixtureRate = $this->getFixtureTaxRates()[0];
        $taxRateId = $fixtureRate->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRateId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteTaxRate'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, ['rateId' => $taxRateId]);
        $this->assertTrue($result);
        /** Ensure that tax rate was actually removed from DB */
        /** @var \Magento\Tax\Model\Calculation\Rate $taxRate */
        $taxRate = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rate');
        $this->assertNull($taxRate->load($taxRateId)->getId(), 'Tax rate was not deleted from DB.');
    }

    /**
     * Insure that tax rate cannot be deleted if it is used for a tax rule.
     *
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCannotDeleteTaxRate()
    {
        $fixtureRate = $this->getFixtureTaxRates()[0];
        $taxRateId = $fixtureRate->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRateId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteTaxRate'
            ]
        ];
        try {
            $this->_webApiCall($serviceInfo, ['rateId' => $taxRateId]);
            $this->fail('Expected exception was not raised');
        } catch (\Exception $e) {
            $expectedMessage = 'The tax rate cannot be removed. It exists in a tax rule.';

            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    /**
     * Get tax rates created in Magento\Tax\_files\tax_classes.php
     *
     * @return \Magento\Tax\Model\Calculation\Rate[]
     */
    private function getFixtureTaxRates()
    {
        if (is_null($this->fixtureTaxRates)) {
            $this->fixtureTaxRates = [];
            if ($this->getFixtureTaxRules()) {
                $taxRateIds = (array)$this->getFixtureTaxRules()[0]->getRates();
                foreach ($taxRateIds as $taxRateId) {
                    /** @var \Magento\Tax\Model\Calculation\Rate $taxRate */
                    $taxRate = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rate');
                    $this->fixtureTaxRates[] = $taxRate->load($taxRateId);
                }
            }
        }
        return $this->fixtureTaxRates;
    }

    /**
     * Get tax classes created in Magento\Tax\_files\tax_classes.php
     *
     * @return \Magento\Tax\Model\ClassModel[]
     */
    private function getFixtureTaxClasses()
    {
        if (is_null($this->fixtureTaxClasses)) {
            $this->fixtureTaxClasses = [];
            if ($this->getFixtureTaxRules()) {
                $taxClassIds = array_merge(
                    (array)$this->getFixtureTaxRules()[0]->getCustomerTaxClasses(),
                    (array)$this->getFixtureTaxRules()[0]->getProductTaxClasses()
                );
                foreach ($taxClassIds as $taxClassId) {
                    /** @var \Magento\Tax\Model\ClassModel $taxClass */
                    $taxClass = Bootstrap::getObjectManager()->create('Magento\Tax\Model\ClassModel');
                    $this->fixtureTaxClasses[] = $taxClass->load($taxClassId);
                }
            }
        }
        return $this->fixtureTaxClasses;
    }

    /**
     * Get tax rule created in Magento\Tax\_files\tax_classes.php
     *
     * @return \Magento\Tax\Model\Calculation\Rule[]
     */
    private function getFixtureTaxRules()
    {
        if (is_null($this->fixtureTaxRules)) {
            $this->fixtureTaxRules = [];
            $taxRuleCodes = ['Test Rule Duplicate', 'Test Rule'];
            foreach ($taxRuleCodes as $taxRuleCode) {
                /** @var \Magento\Tax\Model\Calculation\Rule $taxRule */
                $taxRule = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rule');
                $taxRule->load($taxRuleCode, 'code');
                if ($taxRule->getId()) {
                    $this->fixtureTaxRules[] = $taxRule;
                }
            }
        }
        return $this->fixtureTaxRules;
    }
}

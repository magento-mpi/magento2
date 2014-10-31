<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Data\SearchCriteriaBuilder;
use Magento\Tax\Service\V1\Data\TaxRule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as HttpConstants;

class TaxRuleServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "taxTaxRuleServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/taxRules";

    /** @var \Magento\Tax\Model\Calculation\Rate[] */
    private $fixtureTaxRates;

    /** @var \Magento\Tax\Model\ClassModel[] */
    private $fixtureTaxClasses;

    /** @var \Magento\Tax\Model\Calculation\Rule[] */
    private $fixtureTaxRules;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Data\SearchCriteriaBuilder'
        );
        $this->filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );
        $objectManager = Bootstrap::getObjectManager();
        $this->taxRateService = $objectManager->get('Magento\Tax\Service\V1\TaxRuleService');

        $this->searchCriteriaBuilder = $objectManager->create(
            'Magento\Framework\Data\SearchCriteriaBuilder'
        );
        $this->filterBuilder = $objectManager->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );

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

    public function testCreateTaxRule()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => TaxRuleServiceTest::RESOURCE_PATH,
                'httpMethod' => HttpConstants::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateTaxRule'
            ]
        ];
        $requestData = [
            'rule' => [
                'code' => 'Test Rule ' . microtime(),
                'sort_order' => 10,
                'priority' => 5,
                'customer_tax_class_ids' => [3],
                'product_tax_class_ids' => [2],
                'tax_rate_ids' => [1, 2],
                'calculate_subtotal' => 1
            ]
        ];
        $taxRuleData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('id', $taxRuleData, "Tax rule ID is expected");
        $this->assertGreaterThan(0, $taxRuleData['id']);
        $taxRuleId = $taxRuleData['id'];
        unset($taxRuleData['id']);
        $this->assertEquals($requestData['rule'], $taxRuleData, "Tax rule is created with invalid data.");
        /** Ensure that tax rule was actually created in DB */
        /** @var \Magento\Tax\Model\Calculation\Rule $taxRule */
        $taxRule = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rule');
        $this->assertEquals($taxRuleId, $taxRule->load($taxRuleId)->getId(), 'Tax rule was not created in DB.');
        $taxRule->delete();
    }

    public function testCreateTaxRuleInvalidTaxClassIds()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => TaxRuleServiceTest::RESOURCE_PATH,
                'httpMethod' => HttpConstants::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateTaxRule'
            ]
        ];
        $requestData = [
            'rule' => [
                'code' => 'Test Rule ' . microtime(),
                'sort_order' => 10,
                'priority' => 5,
                'customer_tax_class_ids' => [2],
                'product_tax_class_ids' => [3],
                'tax_rate_ids' => [1, 2],
                'calculate_subtotal' => 1
            ]
        ];


        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Did not throw expected InputException');
        } catch (\SoapFault $e) {
            $this->assertContains('One or more input exceptions have occurred.', $e->getMessage());
        } catch (\Exception $e) {
            $this->assertContains('One or more input exceptions have occurred.', $e->getMessage());
            $this->assertContains('{"fieldName":"customer_tax_class_ids","value":2}', $e->getMessage());
            $this->assertContains('{"fieldName":"product_tax_class_ids","value":3}', $e->getMessage());
        }
    }

    public function testCreateTaxRuleExistingCode()
    {
        $requestData = [
            'rule' => [
                'code' => 'Test Rule ' . microtime(),
                'sort_order' => 10,
                'priority' => 5,
                'customer_tax_class_ids' => [3],
                'product_tax_class_ids' => [2],
                'tax_rate_ids' => [1, 2],
                'calculate_subtotal' => 0
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
                'operation' => self::SERVICE_NAME . 'CreateTaxRule'
            ]
        ];
        $newTaxRuleData = $this->_webApiCall($serviceInfo, $requestData);
        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Expected exception was not raised');
        } catch (\Exception $e) {
            $expectedMessage = 'Code already exists.';
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }

        // Clean up the new tax rule so it won't affect other tests
        /** @var \Magento\Tax\Model\Calculation\Rule $taxRule */
        $taxRule = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rule');
        $taxRule->load($newTaxRuleData['id']);
        $taxRule->delete();
    }

    /**
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetTaxRule()
    {
        $fixtureRule = $this->getFixtureTaxRules()[0];
        $taxRuleId = $fixtureRule->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRuleId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetTaxRule'
            ]
        ];

        $expectedRuleData = [
            'id' => $taxRuleId,
            'code' => 'Test Rule Duplicate',
            'priority' => '0',
            'sort_order' => '0',
            'customer_tax_class_ids' => array_values(array_unique($fixtureRule->getCustomerTaxClasses())),
            'product_tax_class_ids' => array_values(array_unique($fixtureRule->getProductTaxClasses())),
            'tax_rate_ids' => array_values(array_unique($fixtureRule->getRates())),
            'calculate_subtotal' => 0
        ];
        $requestData = ['ruleId' => $taxRuleId];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($expectedRuleData, $result);
    }

    /**
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testSearchTaxRulesSimple()
    {
        // Find rules whose code is 'Test Rule'
        $filter = $this->filterBuilder->setField(TaxRule::CODE)
            ->setValue('Test Rule')
            ->create();

        $this->searchCriteriaBuilder->addFilter([$filter]);

        $fixtureRule = $this->getFixtureTaxRules()[1];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchTaxRules'
            ]
        ];
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];

        /** @var \Magento\Framework\Service\V1\Data\SearchResults $searchResults */
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals(1, $searchResults['total_count']);

        $expectedRuleData = [
            [
                'id' => $fixtureRule->getId(),
                'code' => 'Test Rule',
                'priority' => 0,
                'sort_order' => 0,
                'priority' => 0,
                'sort_order' => 0,
                'calculate_subtotal' => 0,
                'customer_tax_class_ids' => array_values(array_unique($fixtureRule->getCustomerTaxClasses())),
                'product_tax_class_ids' => array_values(array_unique($fixtureRule->getProductTaxClasses())),
                'tax_rate_ids' => array_values(array_unique($fixtureRule->getRates()))
            ]
        ];
        $this->assertEquals($expectedRuleData, $searchResults['items']);
    }

    /**
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testSearchTaxRulesCodeLike()
    {
        // Find rules whose code starts with 'Test Rule'
        $filter = $this->filterBuilder
            ->setField(TaxRule::CODE)
            ->setValue('Test Rule%')
            ->setConditionType('like')
            ->create();

        $sortFilter = $this->filterBuilder
            ->setField(TaxRule::SORT_ORDER)
            ->setValue(0)
            ->create();

        $this->searchCriteriaBuilder->addFilter([$filter, $sortFilter]);

        $fixtureRule = $this->getFixtureTaxRules()[1];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchTaxRules'
            ]
        ];
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];

        /** @var \Magento\Framework\Service\V1\Data\SearchResults $searchResults */
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals(2, $searchResults['total_count']);

        $expectedRuleData = [
            [
                'id' => $fixtureRule->getId(),
                'code' => 'Test Rule',
                'priority' => 0,
                'sort_order' => 0,
                'calculate_subtotal' => 0,
                'customer_tax_class_ids' => array_values(array_unique($fixtureRule->getCustomerTaxClasses())),
                'product_tax_class_ids' => array_values(array_unique($fixtureRule->getProductTaxClasses())),
                'tax_rate_ids' => array_values(array_unique($fixtureRule->getRates()))
            ],
            [
                'id' => $this->getFixtureTaxRules()[0]->getId(),
                'code' => 'Test Rule Duplicate',
                'priority' => 0,
                'sort_order' => 0,
                'calculate_subtotal' => 0,
                'customer_tax_class_ids' => array_values(array_unique($fixtureRule->getCustomerTaxClasses())),
                'product_tax_class_ids' => array_values(array_unique($fixtureRule->getProductTaxClasses())),
                'tax_rate_ids' => array_values(array_unique($fixtureRule->getRates()))
            ],
        ];
        $this->assertEquals($expectedRuleData, $searchResults['items']);
    }

    public function testGetTaxRuleNotExist()
    {
        $taxRuleId = 37865;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRuleId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetTaxRule'
            ]
        ];
        $requestData = ['ruleId' => $taxRuleId];
        try {
            $this->_webApiCall($serviceInfo, $requestData);
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
    public function testUpdateTaxRule()
    {
        $fixtureRule = $this->getFixtureTaxRules()[0];
        $requestData = [
            'rule' => [
                'id' => $fixtureRule->getId(),
                'code' => 'Test Rule ' . microtime(),
                'sort_order' => 10,
                'priority' => 5,
                'customer_tax_class_ids' => [3],
                'product_tax_class_ids' => [2],
                'tax_rate_ids' => [1, 2],
                'calculate_subtotal' => 1
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
                'operation' => self::SERVICE_NAME . 'UpdateTaxRule'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
        $expectedRuleData = $requestData['rule'];
        /** Ensure that tax rule was actually updated in DB */
        /** @var \Magento\Tax\Model\Calculation $taxCalculation */
        $taxCalculation = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation');
        /** @var \Magento\Tax\Model\Calculation\Rule $taxRule */
        $taxRule = Bootstrap::getObjectManager()->create(
            'Magento\Tax\Model\Calculation\Rule',
            ['calculation' => $taxCalculation]
        );
        $taxRuleModel = $taxRule->load($fixtureRule->getId());
        $this->assertEquals($expectedRuleData['id'], $taxRuleModel->getId(), 'Tax rule was not updated in DB.');
        $this->assertEquals(
            $expectedRuleData['code'],
            $taxRuleModel->getCode(),
            'Tax rule code was updated incorrectly.'
        );
        $this->assertEquals(
            $expectedRuleData['sort_order'],
            $taxRuleModel->getPosition(),
            'Tax rule sort order was updated incorrectly.'
        );
        $this->assertEquals(
            $expectedRuleData['priority'],
            $taxRuleModel->getPriority(),
            'Tax rule priority was updated incorrectly.'
        );
        $this->assertEquals(
            $expectedRuleData['customer_tax_class_ids'],
            array_values(array_unique($taxRuleModel->getCustomerTaxClasses())),
            'Customer Tax classes were updated incorrectly'
        );
        $this->assertEquals(
            $expectedRuleData['product_tax_class_ids'],
            array_values(array_unique($taxRuleModel->getProductTaxClasses())),
            'Product Tax classes were updated incorrectly.'
        );
        $this->assertEquals(
            $expectedRuleData['tax_rate_ids'],
            array_values(array_unique($taxRuleModel->getRates())),
            'Tax rates were updated incorrectly.'
        );
    }

    public function testUpdateTaxRuleNotExisting()
    {
        $requestData = [
            'rule' => [
                'id' => 12345,
                'code' => 'Test Rule ' . microtime(),
                'sort_order' => 10,
                'priority' => 5,
                'customer_tax_class_ids' => [3],
                'product_tax_class_ids' => [2],
                'tax_rate_ids' => [1, 2]
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
                'operation' => self::SERVICE_NAME . 'UpdateTaxRule'
            ]
        ];
        try {
            $this->_webApiCall($serviceInfo, $requestData);
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
    public function testDeleteTaxRule()
    {
        $fixtureRule = $this->getFixtureTaxRules()[0];
        $taxRuleId = $fixtureRule->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$taxRuleId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteTaxRule'
            ]
        ];
        $requestData = ['ruleId' => $taxRuleId];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
        /** Ensure that tax rule was actually removed from DB */
        /** @var \Magento\Tax\Model\Calculation\Rule $taxRule */
        $taxRate = Bootstrap::getObjectManager()->create('Magento\Tax\Model\Calculation\Rate');
        $this->assertNull($taxRate->load($taxRuleId)->getId(), 'Tax rule was not deleted from DB.');
    }

    /**
     * @magentoApiDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testSearchTaxRule()
    {
        $fixtureRule = $this->getFixtureTaxRules()[0];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchTaxRules'
            ]
        ];

        $filter = $this->filterBuilder->setField(TaxRule::CODE)
            ->setValue($fixtureRule->getCode())
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter]);
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($fixtureRule->getId(), $searchResults['items'][0][TaxRule::ID]);
        $this->assertEquals($fixtureRule->getCode(), $searchResults['items'][0][TaxRule::CODE]);
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

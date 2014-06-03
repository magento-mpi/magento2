<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Tax\Service\V1\Data\TaxRule;
use Magento\Framework\Exception\NoSuchEntityException;

class TaxRuleServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Tax\Service\V1\Data\TaxRuleBuilder
     */
    private $taxRuleBuilder;

    /**
     * TaxRateService
     *
     * @var \Magento\Tax\Service\V1\TaxRuleServiceInterface
     */
    private $taxRuleService;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->taxRuleService = $this->objectManager->get('Magento\Tax\Service\V1\TaxRuleServiceInterface');
        $this->taxRuleBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxRuleBuilder');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateTaxRule()
    {
        // Tax rule data object created
        $taxRuleDataObject = $this->createTaxRuleDataObject();
        //Tax rule service call
        $taxRuleServiceData = $this->taxRuleService->createTaxRule($taxRuleDataObject);

        //Assertions
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRule', $taxRuleServiceData);
        $this->assertEquals($taxRuleDataObject->getCode(), $taxRuleServiceData->getCode());
        $this->assertEquals(
            $taxRuleDataObject->getCustomerTaxClassIds(),
            $taxRuleServiceData->getCustomerTaxClassIds()
        );
        $this->assertEquals($taxRuleDataObject->getProductTaxClassIds(), $taxRuleServiceData->getProductTaxClassIds());
        $this->assertEquals($taxRuleDataObject->getPriority(), $taxRuleServiceData->getPriority());
        $this->assertEquals($taxRuleDataObject->getSortOrder(), $taxRuleServiceData->getSortOrder());
        $this->assertNotNull($taxRuleServiceData->getId());
    }

    /**
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testDeleteTaxRule()
    {
        /** @var $registry \Magento\Framework\Registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        /** @var $taxRule \Magento\Tax\Model\Calculation\Rule */
        $taxRule = $registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $this->assertNotNull($taxRule);
        $ruleId = $taxRule->getId();

        // Delete the new tax rate
        $this->assertTrue($this->taxRuleService->deleteTaxRule($ruleId));

        // Get the new tax rate, this should fail
        try {
            $this->taxRuleService->getTaxRule($ruleId);
            $this->fail('NoSuchEntityException expected but not thrown');
        } catch(NoSuchEntityException $e) {
            $expectedParams = [
                'fieldName' => 'taxRuleId',
                'fieldValue' => $ruleId,
            ];
            $this->assertEquals($expectedParams, $e->getParameters());
        }
    }

    /**
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testDeleteTaxRateException()
    {
        /** @var $registry \Magento\Framework\Registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        /** @var $taxRule \Magento\Tax\Model\Calculation\Rule */
        $taxRule = $registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $this->assertNotNull($taxRule);
        $ruleId = $taxRule->getId();

        // Delete the new tax rate
        $this->assertTrue($this->taxRuleService->deleteTaxRule($ruleId));

        // Delete the new tax rate again, this should fail
        try {
            $this->taxRuleService->deleteTaxRule($ruleId);
            $this->fail('NoSuchEntityException expected but not thrown');
        } catch(NoSuchEntityException $e) {
            $expectedParams = [
                'fieldName' => 'taxRuleId',
                'fieldValue' => $ruleId,
            ];
            $this->assertEquals($expectedParams, $e->getParameters());
        }
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testUpdateTaxRule()
    {

        $taxRule = $this->createTaxRuleDataObject();
        //Tax rule service call
        $taxRuleServiceData = $this->taxRuleService->createTaxRule($taxRule);

        $updatedTaxRule = $this->taxRuleBuilder->populate($taxRuleServiceData)
            ->setCode('updated code')
            ->create();

        $this->taxRuleService->updateTaxRule($updatedTaxRule);
        $retrievedRule = $this->taxRuleService->getTaxRule($taxRuleServiceData->getId());

        //Assertion
        $this->assertEquals($retrievedRule->__toArray(), $updatedTaxRule->__toArray());
        $this->assertNotEquals($retrievedRule->__toArray(), $taxRule->__toArray());
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage taxRuleId =
     */
    public function testUpdateTaxRuleNoId()
    {
        $this->taxRuleService->updateTaxRule($this->createTaxRuleDataObject());
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage code
     */
    public function testUpdateTaxRuleMissingRequiredFields()
    {
        $taxRuleServiceData = $this->taxRuleService->createTaxRule($this->createTaxRuleDataObject());
        $updatedTaxRule = $this->taxRuleBuilder->populate($taxRuleServiceData)
            ->setCode(null)
            ->create();

        $this->taxRuleService->updateTaxRule($updatedTaxRule);
    }

    /*
     * Creates Tax Rule Data Object
     *
     * @return \Magento\Tax\Service\V1\Data\TaxRule
     */
    private function createTaxRuleDataObject()
    {
        return $this->taxRuleBuilder
            ->setCode('code')
            ->setCustomerTaxClassIds([3])
            ->setProductTaxClassIds([2])
            ->setTaxRateIds([2])
            ->setPriority(0)
            ->setSortOrder(1)
            ->create();
    }
}

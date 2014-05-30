<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\ObjectManager;

class TaxRuleServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRuleServiceInterface
     */
    private $taxRuleService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\TaxRuleRegistry
     */
    private $ruleRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\TaxRuleConverter
     */
    private $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rule
     */
    private $ruleModelMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->ruleRegistryMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\TaxRuleRegistry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->converterMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\TaxRuleConverter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleModelMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rule')
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxRuleService = $this->objectManager->getObject(
            'Magento\Tax\Service\V1\TaxRuleService',
            [
                'ruleRegistry' => $this->ruleRegistryMock,
                'converter' => $this->converterMock,
            ]
        );
    }

    public function testUpdateTaxRate()
    {
        $taxRuleBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRuleBuilder');
        $taxRule = $taxRuleBuilder
            ->setId(2)
            ->setCode('code')
            ->setCustomerTaxClassIds([3])
            ->setProductTaxClassIds([2])
            ->setTaxRateIds([2])
            ->setPriority(0)
            ->setSortOrder(1)
            ->create();
        $this->converterMock->expects($this->once())
            ->method('createTaxRuleModel')
            ->with($taxRule)
            ->will($this->returnValue($this->ruleModelMock));
        $this->ruleModelMock->expects($this->once())->method('save');

        $result = $this->taxRuleService->updateTaxRule($taxRule);

        $this->assertTrue($result);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testUpdateTaxRuleNoId()
    {
        $taxRuleBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRuleBuilder');
        $taxRule = $taxRuleBuilder
            ->setCode('code')
            ->setCustomerTaxClassIds([3])
            ->setProductTaxClassIds([2])
            ->setTaxRateIds([2])
            ->setPriority(0)
            ->setSortOrder(1)
            ->create();

        $this->converterMock->expects($this->once())
            ->method('createTaxRuleModel')
            ->with($taxRule)
            ->will($this->throwException(new NoSuchEntityException()));

        $this->taxRuleService->updateTaxRule($taxRule);
    }

}

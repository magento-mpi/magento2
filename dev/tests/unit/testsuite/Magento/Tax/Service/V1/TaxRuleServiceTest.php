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
        $this->taxRuleService = $this->objectManager->getObject('Magento\Tax\Service\V1\TaxRuleService',
            [
                'taxRuleRegistry' => $this->ruleRegistryMock,
                'converter' => $this->converterMock,
            ]
        );
    }

    public function testDeleteTaxRule()
    {
        $this->ruleRegistryMock->expects($this->once())
            ->method('retrieveTaxRule')
            ->with(1)
            ->will($this->returnValue($this->ruleModelMock));
        $this->ruleRegistryMock->expects($this->once())
            ->method('removeTaxRule')
            ->with(1);
        $this->taxRuleService->deleteTaxRule(1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteTaxRuleRetrieveException()
    {
        $this->ruleRegistryMock->expects($this->once())
            ->method('retrieveTaxRule')
            ->with(1)
            ->will($this->throwException(new NoSuchEntityException()));
        $this->taxRuleService->deleteTaxRule(1);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Bad error occurred
     */
    public function testDeleteTaxRuleDeleteException()
    {
        $this->ruleRegistryMock->expects($this->once())
            ->method('retrieveTaxRule')
            ->with(1)
            ->will($this->returnValue($this->ruleModelMock));
        $this->ruleModelMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception('Bad error occurred')));
        $this->taxRuleService->deleteTaxRule(1);
    }

}

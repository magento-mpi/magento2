<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model;


class TaxRuleRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Model\TaxRuleRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxRuleRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    protected function setUp()
    {
        $this->taxRuleRegistry = $this->getMock('\Magento\Tax\Model\Calculation\TaxRuleRegistry', [], [], '', false);
        $this->searchResultBuilder = $this->getMock(
            '\Magento\Tax\Api\Data\TaxRuleSearchResultsDataBuilder', [], [], '', false
        );
        $this->ruleFactory = $this->getMock('\Magento\Tax\Model\Calculation\RuleFactory', [], [], '', false);
        $this->collectionFactory = $this->getMock('\Magento\Tax\Model\Resource\Calculation\Rule\CollectionFactory', [], [], '', false);
        $this->resource = $this->getMock('\Magento\Tax\Model\Resource\Calculation\Rule', [], [], '', false);

        $this->model = new TaxRuleRepository($this->taxRuleRegistry, $this->searchResultBuilder, $this->ruleFactory, $this->collectionFactory, $this->resource);
    }

    public function testGet()
    {
        $rule = $this->getMock('\Magento\Tax\Model\Calculation\Rule', [], [], '', false);
        $this->taxRuleRegistry->expects($this->once())->method('retrieveTaxRule')->with(10)->willReturn($rule);
        $this->assertEquals($rule, $this->model->get(10));
    }

    public function testDelete()
    {
        $rule = $this->getMock('\Magento\Tax\Model\Calculation\Rule', [], [], '', false);
        $rule->expects($this->once())->method('getId')->willReturn(10);
        $this->resource->expects($this->once())->method('delete')->with($rule);
        $this->taxRuleRegistry->expects($this->once())->method('removeTaxRule')->with(10);
        $this->assertTrue($this->model->delete($rule));
    }
}

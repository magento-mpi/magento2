<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Model\Condition\Combine;

class AbstractCombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCombine
     */
    protected $model;

    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $resourceSegment;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->conditionFactory = $this->getMock('Magento\CustomerSegment\Model\ConditionFactory', [], [], '', false);
        $this->resourceSegment = $this->getMock('Magento\CustomerSegment\Model\Resource\Segment', [], [], '', false);

        $this->model = $this->getMockForAbstractClass(
            'Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine',
            [
                $this->context,
                $this->conditionFactory,
                $this->resourceSegment,
            ]
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->context,
            $this->conditionFactory,
            $this->resourceSegment
        );
    }

    public function testGetMatchedEvents()
    {
        $result = $this->model->getMatchedEvents();

        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testGetDefaultOperatorInputByType()
    {
        $result = $this->model->getDefaultOperatorInputByType();

        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('string', $result);
        $this->assertTrue(is_array($result['string']));
        $this->assertEquals(['==', '!=', '{}', '!{}'], $result['string']);

        $this->assertArrayHasKey('numeric', $result);
        $this->assertTrue(is_array($result['numeric']));
        $this->assertEquals(['==', '!=', '>=', '>', '<=', '<'], $result['numeric']);

        $this->assertArrayHasKey('date', $result);
        $this->assertTrue(is_array($result['date']));
        $this->assertEquals(['==', '>=', '<='], $result['date']);

        $this->assertArrayHasKey('select', $result);
        $this->assertTrue(is_array($result['select']));
        $this->assertEquals(['==', '!='], $result['select']);

        $this->assertArrayHasKey('boolean', $result);
        $this->assertTrue(is_array($result['boolean']));
        $this->assertEquals(['==', '!='], $result['boolean']);

        $this->assertArrayHasKey('multiselect', $result);
        $this->assertTrue(is_array($result['multiselect']));
        $this->assertEquals(['{}', '!{}', '()', '!()'], $result['multiselect']);

        $this->assertArrayHasKey('grid', $result);
        $this->assertTrue(is_array($result['grid']));
        $this->assertEquals(['()', '!()'], $result['grid']);
    }

    public function testLoadArray()
    {
        $data = [
            'operator' => 'test_operator',
            'attribute' => 'test_attribute',
        ];

        $result = $this->model->loadArray($data);

        $this->assertEquals($data['operator'], $result->getOperator());
        $this->assertEquals($data['attribute'], $result->getAttribute());
    }

    public function testGetResource()
    {
        $result = $this->model->getResource();

        $this->assertInstanceOf('Magento\CustomerSegment\Model\Resource\Segment', $result);
        $this->assertEquals($this->resourceSegment, $result);
    }

    public function testGetIsRequired()
    {
        $this->model->setValue(1);

        $this->assertTrue($this->model->getIsRequired());

        $this->model->setValue(0);

        $this->assertFalse($this->model->getIsRequired());
    }

    public function testGetCombineProductCondition()
    {
        $this->assertFalse($this->model->getCombineProductCondition());
    }
}

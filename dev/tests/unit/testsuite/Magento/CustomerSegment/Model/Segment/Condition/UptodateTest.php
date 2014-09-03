<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model\Segment\Condition;

class UptodateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Uptodate
     */
    protected $model;

    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $resourceSegment;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceSegment = $this->getMock('Magento\CustomerSegment\Model\Resource\Segment', [], [], '', false);

        $this->model = new Uptodate(
            $this->context,
            $this->resourceSegment
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->context,
            $this->resourceSegment
        );
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
        $this->assertEquals(['>=', '<=', '>', '<'], $result['numeric']);

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
        $this->assertEquals(['==', '!=', '[]', '![]'], $result['multiselect']);

        $this->assertArrayHasKey('grid', $result);
        $this->assertTrue(is_array($result['grid']));
        $this->assertEquals(['()', '!()'], $result['grid']);
    }

    public function testGetDefaultOperatorOptions()
    {
        $result = $this->model->getDefaultOperatorOptions();

        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('<=', $result);
        $this->assertEquals(__('equals or greater than'), $result['<=']);

        $this->assertArrayHasKey('>=', $result);
        $this->assertEquals(__('equals or less than'), $result['>=']);

        $this->assertArrayHasKey('<', $result);
        $this->assertEquals(__('greater than'), $result['<']);

        $this->assertArrayHasKey('>', $result);
        $this->assertEquals(__('less than'), $result['>']);
    }

    public function testGetNewChildSelectOptions()
    {
        $type = 'test_type';
        $this->model->setType($type);

        $result = $this->model->getNewChildSelectOptions();

        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals($type, $result['value']);

        $this->assertArrayHasKey('label', $result);
        $this->assertEquals(__('Up To Date'), $result['label']);
    }

    public function testGetValueElementType()
    {
        $this->assertEquals('text', $this->model->getValueElementType());
    }

    public function testGetSubfilterType()
    {
        $this->assertEquals('date', $this->model->getSubfilterType());
    }
}

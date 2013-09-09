<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerSegment_Model_Plugin_SalesRuleConditionCombineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CustomerSegment_Model_Plugin_SalesRuleConditionCombine
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_segmentHelperMock;

    /**
     * @var array
     */
    protected $_conditions;

    protected function setUp()
    {
        $this->_conditions = array(
            array(
                'label' => 'Label',
                'value' => 'Class_Name'
            )
        );

        $this->_segmentHelperMock = $this->getMock(
            'Magento_CustomerSegment_Helper_Data', array(), array(), '', false
        );

        $this->_model = new Magento_CustomerSegment_Model_Plugin_SalesRuleConditionCombine($this->_segmentHelperMock);
    }

    /**
     * @covers Magento_CustomerSegment_Model_Plugin_SalesRuleConditionCombine::afterGetNewChildSelectOptions
     */
    public function testAfterGetNewChildSelectOptionsHelperDisabled()
    {
        $this->_segmentHelperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $this->assertEquals($this->_conditions, $this->_model->afterGetNewChildSelectOptions($this->_conditions));
    }

    /**
     * @covers Magento_CustomerSegment_Model_Plugin_SalesRuleConditionCombine::afterGetNewChildSelectOptions
     */
    public function testAfterGetNewChildSelectOptionsHelperEnabled()
    {
        $this->_segmentHelperMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $updatedConditions = $this->_model->afterGetNewChildSelectOptions($this->_conditions);
        $this->assertTrue(count($updatedConditions) > count($this->_conditions));
    }
}

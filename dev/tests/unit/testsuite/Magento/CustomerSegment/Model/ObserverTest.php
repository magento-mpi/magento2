<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CustomerSegment_Model_Observer
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    protected function setUp()
    {
        $this->_segmentHelper = $this->getMock(
            'Magento_CustomerSegment_Helper_Data', array('isEnabled', 'addSegmentFieldsToForm'), array(), '', false
        );
        $this->_model = new Magento_CustomerSegment_Model_Observer($this->_segmentHelper);
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_segmentHelper = null;
    }

    public function testAddFieldsToTargetRuleForm()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $formDependency = $this->getMock(
            'Magento_Backend_Block_Widget_Form_Element_Dependence', array(), array(), '', false
        );

        $layout = $this->getMock('Magento_Core_Model_Layout', array('createBlock'), array(), '', false);
        $layout
            ->expects($this->once())
            ->method('createBlock')
            ->with('Magento_Backend_Block_Widget_Form_Element_Dependence')
            ->will($this->returnValue($formDependency))
        ;

        $form = new \Magento\Data\Form();
        $model = new \Magento\Object();
        $block = new \Magento\Object(array('layout' => $layout));

        $this->_segmentHelper
            ->expects($this->once())->method('addSegmentFieldsToForm')->with($form, $model, $formDependency);

        $this->_model->addFieldsToTargetRuleForm(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('form' => $form, 'model' => $model, 'block' => $block)),
        )));
    }

    public function testAddFieldsToTargetRuleFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $layout = $this->getMock('Magento_Core_Model_Layout', array('createBlock'), array(), '', false);
        $layout->expects($this->never())->method('createBlock');

        $form = new \Magento\Data\Form();
        $model = new \Magento\Object();
        $block = new \Magento\Object(array('layout' => $layout));

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToTargetRuleForm(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('form' => $form, 'model' => $model, 'block' => $block)),
        )));
    }
}

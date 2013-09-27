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
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $this->_model = new Magento_CustomerSegment_Model_Observer(
            $this->getMock('Magento_Core_Model_StoreManagerInterface', array(), array(), '', false),
            $this->getMock('Magento_Customer_Model_Session', array(), array(), '', false),
            $this->getMock('Magento_CustomerSegment_Model_Customer', array(), array(), '', false),
            $this->getMock('Magento_Backend_Model_Config_Source_Yesno', array(), array(), '', false),
            $this->_segmentHelper,
            $coreRegistry
        );
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

        $factoryElement = $this->getMock('Magento_Data_Form_Element_Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento_Data_Form_Element_CollectionFactory', array('create'),
            array(), '', false);
        $session = $this->getMock('Magento_Core_Model_Session', array(), array(), '', false);
        $form = new Magento_Data_Form($session, $factoryElement, $collectionFactory);
        $model = new Magento_Object();
        $block = new Magento_Object(array('layout' => $layout));

        $this->_segmentHelper
            ->expects($this->once())->method('addSegmentFieldsToForm')->with($form, $model, $formDependency);

        $this->_model->addFieldsToTargetRuleForm(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('form' => $form, 'model' => $model, 'block' => $block)),
        )));
    }

    public function testAddFieldsToTargetRuleFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $layout = $this->getMock('Magento_Core_Model_Layout', array('createBlock'), array(), '', false);
        $layout->expects($this->never())->method('createBlock');

        $factoryElement = $this->getMock('Magento_Data_Form_Element_Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento_Data_Form_Element_CollectionFactory', array('create'),
            array(), '', false);
        $session = $this->getMock('Magento_Core_Model_Session', array(), array(), '', false);
        $form = new Magento_Data_Form($session, $factoryElement, $collectionFactory);
        $model = new Magento_Object();
        $block = new Magento_Object(array('layout' => $layout));

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToTargetRuleForm(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('form' => $form, 'model' => $model, 'block' => $block)),
        )));
    }
}

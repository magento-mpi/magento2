<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Observer_Grid
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitationValidator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitation;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_block;

    /**
     * @var Magento_Event_Observer
     */
    private $_eventArgument;

    protected function setUp()
    {
        $this->_limitationValidator = $this->getMock(
            'Saas_Limitation_Model_Limitation_Validator', array('exceedsThreshold'), array(), '', false
        );
        $this->_limitation = $this->getMockForAbstractClass('Saas_Limitation_Model_Limitation_LimitationInterface');
        $this->_block = $this->getMock(
            'Magento_Backend_Block_Widget_Container', array('updateButton', 'removeButton'), array(), '', false
        );
        $this->_eventArgument = new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('block' => $this->_block))
        ));
        $this->_model = new Saas_Limitation_Model_Observer_Grid(
            $this->_limitationValidator,
            $this->_limitation,
            get_class($this->_block),
            'test_button'
        );
    }

    /**
     * Emulate whether a threshold has been reached or not
     *
     * @param bool $isThresholdReached
     */
    protected function _emulateThresholdIsReached($isThresholdReached)
    {
        $this->_limitationValidator
            ->expects($this->any())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue($isThresholdReached))
        ;
    }

    public function testDisableButtonActive()
    {
        $this->_emulateThresholdIsReached(true);
        $this->_block->expects($this->once())->method('updateButton')->with('test_button', 'disabled', true);
        $this->_model->disableButton($this->_eventArgument);
    }

    public function testDisableButtonInactiveRelevantBlock()
    {
        $this->_emulateThresholdIsReached(false);
        $this->_block->expects($this->never())->method('updateButton');
        $this->_model->disableButton($this->_eventArgument);
    }

    /**
     * @param bool $isThresholdReached
     * @dataProvider thresholdDataProvider
     */
    public function testDisableButtonInactiveIrrelevantBlock($isThresholdReached)
    {
        $this->_emulateThresholdIsReached($isThresholdReached);
        $block = $this->getMock('Magento_Backend_Block_Widget_Container', array('updateButton'), array(), '', false);
        $block->expects($this->never())->method('updateButton');
        $this->_model->disableButton(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('block' => $block))
        )));
    }

    public function testDisableSplitButtonActive()
    {
        $this->_emulateThresholdIsReached(true);
        $this->_block->expects($this->at(0))->method('updateButton')->with('test_button', 'disabled', true);
        $this->_block->expects($this->at(1))->method('updateButton')->with('test_button', 'has_split', false);
        $this->_model->disableSplitButton($this->_eventArgument);
    }

    public function testDisableSplitButtonInactiveRelevantBlock()
    {
        $this->_emulateThresholdIsReached(false);
        $this->_block->expects($this->never())->method('updateButton');
        $this->_model->disableSplitButton($this->_eventArgument);
    }

    /**
     * @param bool $isThresholdReached
     * @dataProvider thresholdDataProvider
     */
    public function testDisableSplitButtonInactiveIrrelevantBlock($isThresholdReached)
    {
        $this->_emulateThresholdIsReached($isThresholdReached);
        $block = $this->getMock('Magento_Backend_Block_Widget_Container', array('updateButton'), array(), '', false);
        $block->expects($this->never())->method('updateButton');
        $this->_model->disableSplitButton(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('block' => $block))
        )));
    }

    public function testRemoveButtonActive()
    {
        $this->_emulateThresholdIsReached(true);
        $this->_block->expects($this->once())->method('removeButton')->with('test_button');
        $this->_model->removeButton($this->_eventArgument);
    }

    public function testRemoveButtonInactiveRelevantBlock()
    {
        $this->_emulateThresholdIsReached(false);
        $this->_block->expects($this->never())->method('removeButton');
        $this->_model->removeButton($this->_eventArgument);
    }

    /**
     * @param bool $isThresholdReached
     * @dataProvider thresholdDataProvider
     */
    public function testRemoveButtonInactiveIrrelevantBlock($isThresholdReached)
    {
        $this->_emulateThresholdIsReached($isThresholdReached);
        $block = $this->getMock('Magento_Backend_Block_Widget_Container', array('removeButton'), array(), '', false);
        $block->expects($this->never())->method('removeButton');
        $this->_model->removeButton(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('block' => $block))
        )));
    }

    public function thresholdDataProvider()
    {
        return array(
            'threshold not reached' => array(false),
            'threshold reached'     => array(true),
        );
    }
}

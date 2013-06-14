<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Product_Observer_Form
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

    protected function setUp()
    {
        $this->_limitationValidator = $this->getMock(
            'Saas_Limitation_Model_Limitation_Validator', array('exceedsThreshold'), array(), '', false
        );
        $this->_limitation = $this->getMockForAbstractClass('Saas_Limitation_Model_Limitation_LimitationInterface');
        $this->_model = new Saas_Limitation_Model_Catalog_Product_Observer_Form(
            $this->_limitationValidator,
            $this->_limitation
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

    /**
     * Retrieve newly created button instance with mocked dependencies
     *
     * @return Mage_Backend_Block_Widget_Button
     */
    protected function _createButton()
    {
        return new Mage_Backend_Block_Widget_Button(
            $this->getMock('Mage_Backend_Block_Template_Context', array(), array(), '', false)
        );
    }

    public function testRemoveSavingButtonsActive()
    {
        $this->_emulateThresholdIsReached(true);

        $button = $this->_createButton();
        $button->setData('options', array(
            array('id' => 'edit-button'),
            array('id' => 'new-button'),
            array('id' => 'duplicate-button'),
            array('id' => 'close-button'),
        ));
        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Product_Edit', array('getProduct', 'getChildBlock'), array(), '', false
        );
        $block->expects($this->once())
            ->method('getChildBlock')->with('save-split-button')->will($this->returnValue($button));

        $this->_model->removeSavingButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));

        $expectedOptions = array(
            array('id' => 'edit-button'),
            array('id' => 'close-button'),
        );
        $this->assertEquals($expectedOptions, $button->getData('options'));
    }

    public function testRemoveSavingButtonsInactiveRelevantBlock()
    {
        $this->_emulateThresholdIsReached(false);

        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Product_Edit', array('getProduct', 'getChildBlock'), array(), '', false
        );
        $block->expects($this->never())->method('getChildBlock');

        $this->_model->removeSavingButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    /**
     * @param bool $isThresholdReached
     * @dataProvider removeSavingButtonsInactiveIrrelevantBlockDataProvider
     */
    public function testRemoveSavingButtonsInactiveIrrelevantBlock($isThresholdReached)
    {
        $this->_emulateThresholdIsReached($isThresholdReached);

        $block = $this->getMock('Mage_Core_Block_Abstract', array('getChildBlock'), array(), '', false);
        $block->expects($this->never())->method('getChildBlock');

        $this->_model->removeSavingButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function removeSavingButtonsInactiveIrrelevantBlockDataProvider()
    {
        return array(
            'threshold not reached' => array(false),
            'threshold reached'     => array(true),
        );
    }
}

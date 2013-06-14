<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Category_Observer_BlockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Category_Observer_Block
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
        $this->_model = new Saas_Limitation_Model_Catalog_Category_Observer_Block(
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

    /**
     * @param bool $isThresholdReached
     * @dataProvider disableCreationButtonsInactiveIrrelevantBlockDataProvider
     */
    public function testDisableCreationButtonsInactiveIrrelevantBlock($isThresholdReached)
    {
        $this->_emulateThresholdIsReached($isThresholdReached);

        $block = $this->getMock('Mage_Core_Block_Abstract', array('getChildBlock'), array(), '', false);
        $block->expects($this->never())->method('getChildBlock');

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function disableCreationButtonsInactiveIrrelevantBlockDataProvider()
    {
        return array(
            'threshold not reached' => array(false),
            'threshold reached'     => array(true),
        );
    }

    public function testDisableCreationButtonsCategoryTreeActive()
    {
        $this->_emulateThresholdIsReached(true);

        $rootButton = $this->_createButton();
        $subButton = $this->_createButton();
        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Tree', array('getChildBlock'), array(), '', false
        );
        $block->expects($this->any())->method('getChildBlock')->will($this->returnValueMap(array(
            array('add_root_button', $rootButton),
            array('add_sub_button', $subButton),
        )));

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));

        $this->assertTrue($rootButton->getData('disabled'));
        $this->assertTrue($subButton->getData('disabled'));
    }

    public function testDisableCreationButtonsCategoryTreeInactiveRelevantBlock()
    {
        $this->_emulateThresholdIsReached(false);

        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Tree', array('getChildBlock'), array(), '', false
        );
        $block->expects($this->never())->method('getChildBlock');

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisableCreationButtonsCategoryFormActive()
    {
        $this->_emulateThresholdIsReached(true);

        $button = $this->_createButton();
        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Edit_Form', array('getCategoryId', 'getChildBlock'),
            array(), '', false
        );
        $block->expects($this->once())->method('getCategoryId')->will($this->returnValue(null));
        $block->expects($this->once())->method('getChildBlock')->with('save_button')->will($this->returnValue($button));

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));

        $this->assertTrue($button->getData('disabled'));
    }

    public function testDisableCreationButtonsCategoryFormInactiveRelevantBlock()
    {
        $this->_emulateThresholdIsReached(false);

        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Edit_Form', array('getChildBlock'),
            array(), '', false
        );
        $block->expects($this->never())->method('getChildBlock');

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisableCreationButtonsCategoryFormInactiveCategoryId()
    {
        $this->_emulateThresholdIsReached(true);

        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Edit_Form', array('getCategoryId', 'getChildBlock'),
            array(), '', false
        );
        $block->expects($this->once())->method('getCategoryId')->will($this->returnValue(6));
        $block->expects($this->never())->method('getChildBlock');

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisableCreationButtonsAddButtonActive()
    {
        $this->_emulateThresholdIsReached(true);

        $button = $this->_createButton();
        $button->setId('add_category_button');

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $button))
        )));

        $this->assertTrue($button->getData('disabled'));
    }

    /**
     * @param bool $isThresholdReached
     * @param string $buttonId
     * @dataProvider disableCreationButtonsAddButtonInactiveDataProvider
     */
    public function testDisableCreationButtonsAddButtonInactive($isThresholdReached, $buttonId)
    {
        $this->_emulateThresholdIsReached($isThresholdReached);

        $button = $this->_createButton();
        $button->setId($buttonId);

        $this->_model->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $button))
        )));

        $this->assertNull($button->getData('disabled'));
    }

    public function disableCreationButtonsAddButtonInactiveDataProvider()
    {
        return array(
            'threshold not reached & relevant button'   => array(false, 'add_category_button'),
            'threshold not reached & irrelevant button' => array(false, 'irrelevant_button'),
            'threshold reached & irrelevant button'     => array(true, 'irrelevant_button'),
        );
    }
}

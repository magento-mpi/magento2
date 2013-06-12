<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Category_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Category_Observer
     */
    protected $_observer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_session;

    /**
     * @var Saas_Limitation_Model_Catalog_Category_Limitation|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_categoryLimitation;

    public function setUp()
    {
        $this->_session = $this->getMock('Mage_Backend_Model_Session', array('addNotice'), array(), '', false);

        $this->_categoryLimitation = $this->getMock('Saas_Limitation_Model_Catalog_Category_Limitation',
            array(), array(), '', false);

        $this->_observer = new Saas_Limitation_Model_Catalog_Category_Observer(
            $this->_session, $this->_categoryLimitation
        );
    }

    /**
     * @param bool $isLimitationReached
     * @param bool $isNewEntity
     * @dataProvider restrictEntityCreationInactiveDataProvider
     */
    public function testRestrictEntityCreationInactive($isLimitationReached, $isNewEntity)
    {
        $this->_categoryLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $this->_categoryLimitation->expects($this->never())->method('getCreateRestrictedMessage');

        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue($isNewEntity));

        $this->_observer->restrictEntityCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $entity))
        )));
    }

    /**
     * @return array
     */
    public function restrictEntityCreationInactiveDataProvider()
    {
        return array(
            'limitation not reached & existing entity'  => array(false, false),
            'limitation not reached & new entity'       => array(false, true),
            'limitation reached & existing entity'      => array(true, false)
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Limitation has been reached
     */
    public function testRestrictEntityCreationActive()
    {
        $this->_categoryLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue(true));

        $this->_categoryLimitation
            ->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Limitation has been reached'))
        ;

        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue(true));

        $this->_observer->restrictEntityCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $entity))
        )));
    }

    /**
     * @param bool $isLimitationReached
     * @param int|null $categoryId
     * @dataProvider disableCreationButtonsInCategoriesFormInactiveDataProvider
     */
    public function testDisableCreationButtonsInCategoriesFormInactive($isLimitationReached, $categoryId)
    {
        $this->_categoryLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Edit_Form',
            array('getChildBlock', 'getCategoryId'), array(), '', false
        );
        $block->expects($this->once())->method('getCategoryId')->will($this->returnValue($categoryId));
        $block->expects($this->never())->method('getChildBlock');

        $this->_observer->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    /**
     * @return array
     */
    public function disableCreationButtonsInCategoriesFormInactiveDataProvider()
    {
        return array(
            'limitation not reached & new category' => array(false, null),
            'limitation not reached & existing category' => array(false, 1),
            'limitation reached & existing category' => array(true, 1),
        );
    }

    /**
     * @param bool $isLimitationReached
     * @param string $blockClass
     * @dataProvider disableCreationButtonsInCategoriesTreeInactiveDataProvider
     */
    public function testDisableCreationButtonsInCategoriesTreeInactive($isLimitationReached, $blockClass)
    {
        $this->_categoryLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $block = $this->getMock($blockClass, array('getChildBlock'), array(), '', false);
        $block->expects($this->never())->method('getChildBlock');

        $this->_observer->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    /**
     * @return array
     */
    public function disableCreationButtonsInCategoriesTreeInactiveDataProvider()
    {
        return array(
            'limitation not reached & relevant block'    => array(false, 'Mage_Adminhtml_Block_Catalog_Category_Tree'),
            'limitation reached & irrelevant block'      => array(true, 'Mage_Backend_Block_Widget_Grid_Container'),
        );
    }

    /**
     * @param string $buttonName
     * @param bool $isLimitationReached
     * @dataProvider disableCreationButtonsInProductsInactiveDataProvider
     */
    public function testDisableCreationButtonsInProductsInactive($buttonName, $isLimitationReached)
    {
        $this->_categoryLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $block = $this->getMock(
            'Mage_Backend_Block_Widget_Button', array('setData', 'getId'), array(), '', false
        );
        $block->expects($this->once())->method('getId')->will($this->returnValue($buttonName));
        $block->expects($this->never())->method('setData');

        $this->_observer->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    /**
     * @return array
     */
    public function disableCreationButtonsInProductsInactiveDataProvider()
    {
        return array(
            'wrong button, no limits' => array('wrong_button', false),
            'right button, no limits' => array('add_category_button', false),
            'wrong button, limits' => array('wrong_button', true),
        );
    }

    public function testDisableCreationButtonsInCategoriesFormActive()
    {
        $this->_categoryLimitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $button = $this->getMock('Mage_Backend_Block_Widget_Button', array('setData'), array(), '', false);
        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Edit_Form',
            array('getCategoryId', 'getChildBlock'), array(), '', false);
        $block->expects($this->once())->method('getCategoryId')->will($this->returnValue(null));
        $block->expects($this->once())->method('getChildBlock')->with('save_button')->will($this->returnValue($button));
        $button->expects($this->once())->method('setData')->with('disabled', true);

        $this->_observer->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisableCreationButtonsInCategoriesTreeActive()
    {
        $this->_categoryLimitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $button = $this->getMock('Mage_Backend_Block_Widget_Button', array('setData'), array(), '', false);

        $block = $this->getMock(
            'Mage_Adminhtml_Block_Catalog_Category_Tree', array('getChildBlock'), array(), '', false
        );
        $block->expects($this->exactly(2))
            ->method('getChildBlock')
            ->with($this->logicalOr('add_root_button', 'add_sub_button'))
            ->will($this->returnValue($button));
        $button->expects($this->exactly(2))->method('setData')->with('disabled', true);

        $this->_observer->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisableCreationButtonsInProductsActive()
    {
        $this->_categoryLimitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $block = $this->getMock(
            'Mage_Backend_Block_Widget_Button', array('setData', 'getId'), array(), '', false
        );
        $block->expects($this->once())->method('getId')->will($this->returnValue('add_category_button'));
        $block->expects($this->once())->method('setData')->with('disabled', true);

        $this->_observer->disableCreationButtons(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisplayNotificationInactive()
    {
        $this->_categoryLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue(false));

        $this->_categoryLimitation->expects($this->never())->method('getCreateRestrictedMessage');

        $this->_session->expects($this->never())->method('addNotice');

        $this->_observer->displayNotification(new Varien_Event_Observer());
    }

    public function testDisplayNotificationActive()
    {
        $this->_categoryLimitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $this->_categoryLimitation
            ->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Limitation has been reached'))
        ;

        $this->_session->expects($this->once())->method('addNotice')->with('Limitation has been reached');

        $this->_observer->displayNotification(new Varien_Event_Observer());
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Store_Observer
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_session;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_storeLimitation;

    protected function setUp()
    {
        $this->_session = $this->getMock('Mage_Backend_Model_Session', array('addNotice'), array(), '', false);
        $this->_storeLimitation = $this->getMock(
            'Saas_Limitation_Model_Store_Limitation',
            array('isCreateRestricted', 'getCreateRestrictedMessage'),
            array(), '', false
        );
        $this->_model = new Saas_Limitation_Model_Store_Observer($this->_session, $this->_storeLimitation);
    }

    /**
     * @param bool $isLimitationReached
     * @param bool $isNewEntity
     * @dataProvider restrictEntityCreationInactiveDataProvider
     */
    public function testRestrictEntityCreationInactive($isLimitationReached, $isNewEntity)
    {
        $this->_storeLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $this->_storeLimitation->expects($this->never())->method('getCreateRestrictedMessage');

        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue($isNewEntity));

        $this->_model->restrictEntityCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $entity))
        )));
    }

    public function restrictEntityCreationInactiveDataProvider()
    {
        return array(
            'limitation not reached & existing entity'  => array(false, false),
            'limitation not reached & new entity'       => array(false, true),
            'limitation reached & existing entity'      => array(true, false),
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Limitation has been reached
     */
    public function testRestrictEntityCreationActive()
    {
        $this->_storeLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue(true));

        $this->_storeLimitation
            ->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Limitation has been reached'))
        ;

        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue(true));

        $this->_model->restrictEntityCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $entity))
        )));
    }

    /**
     * @param bool $isLimitationReached
     * @param string $blockClass
     * @dataProvider disableCreationButtonInactiveDataProvider
     */
    public function testDisableCreationButtonInactive($isLimitationReached, $blockClass)
    {
        $this->_storeLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue($isLimitationReached));

        $block = $this->getMock($blockClass, array('updateButton'), array(), '', false);
        $block->expects($this->never())->method('updateButton');

        $this->_model->disableCreationButton(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function disableCreationButtonInactiveDataProvider()
    {
        return array(
            'limitation not reached & relevant grid'    => array(false, 'Mage_Adminhtml_Block_System_Store_Store'),
            'limitation reached & irrelevant grid'      => array(true, 'Mage_Backend_Block_Widget_Grid_Container'),
        );
    }

    public function testDisableCreationButtonActive()
    {
        $this->_storeLimitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $block = $this->getMock('Mage_Adminhtml_Block_System_Store_Store', array('updateButton'), array(), '', false);
        $block->expects($this->once())->method('updateButton')->with('add_store', 'disabled', true);

        $this->_model->disableCreationButton(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('block' => $block))
        )));
    }

    public function testDisplayNotificationInactive()
    {
        $this->_storeLimitation
            ->expects($this->any())->method('isCreateRestricted')->will($this->returnValue(false));

        $this->_storeLimitation->expects($this->never())->method('getCreateRestrictedMessage');

        $this->_session->expects($this->never())->method('addNotice');

        $this->_model->displayNotification(new Varien_Event_Observer());
    }

    public function testDisplayNotificationActive()
    {
        $this->_storeLimitation
            ->expects($this->once())->method('isCreateRestricted')->will($this->returnValue(true));

        $this->_storeLimitation
            ->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Limitation has been reached'))
        ;

        $this->_session->expects($this->once())->method('addNotice')->with('Limitation has been reached');

        $this->_model->displayNotification(new Varien_Event_Observer());
    }
}

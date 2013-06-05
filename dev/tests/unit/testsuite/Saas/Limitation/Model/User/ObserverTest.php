<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_User_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_limitation;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var Saas_Limitation_Model_User_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_limitation = $this->getMock('Saas_Limitation_Model_User_Limitation', array(), array(), '', false);
        $this->_session = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);
        $this->_model = new Saas_Limitation_Model_User_Observer($this->_limitation, $this->_session);
    }

    protected function tearDown()
    {
        $this->_session = null;
        $this->_limitation = null;
        $this->_model  = null;
    }

    public function testDisableCreationButtonRestricted()
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $block = $this->getMock('Mage_User_Block_User', array('updateButton'), array(), '', false);
        $block->expects($this->once())
            ->method('updateButton')
            ->with('add', 'disabled', true);
        $observer = new Varien_Event_Observer(array('event' => new Varien_Object(array('block' => $block))));
        $this->_model->disableCreationButton($observer);
    }

    /**
     * @param string $blockClass
     * @param bool $isLimitationReached
     * @dataProvider disableCreationButtonNonRestrictedDataProvider
     */
    public function testDisableCreationButtonNonRestricted($blockClass, $isLimitationReached)
    {
        $this->_limitation->expects($this->any())
            ->method('isCreateRestricted')
            ->will($this->returnValue($isLimitationReached));
        $block = $this->getMock($blockClass, array('updateButton'), array(), '', false);
        $block->expects($this->never())
            ->method('updateButton');
        $observer = new Varien_Event_Observer(array('event' => new Varien_Object(array('block' => $block))));
        $this->_model->disableCreationButton($observer);
    }

    /**
     * @return array
     */
    public function disableCreationButtonNonRestrictedDataProvider()
    {
        return array(
            'user block, limitation is not reached'     => array('Mage_User_Block_User', false),
            'non-user block, limitation is not reached' => array('Some_Block', false),
            'non-user block, limitation is reached'     => array('Some_Block', true),
        );
    }

    public function testDisplayNotificationRestricted()
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $restrictionMessage = 'restriction message';
        $this->_limitation->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue($restrictionMessage));
        $this->_session->expects($this->once())
            ->method('addNotice')
            ->with($restrictionMessage);
        $this->_model->displayNotification(new Varien_Event_Observer);
    }

    public function testDisplayNotificationNonRestricted()
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(false));
        $this->_limitation->expects($this->never())
            ->method('getCreateRestrictedMessage');
        $this->_session->expects($this->never())
            ->method('addNotice');
        $this->_model->displayNotification(new Varien_Event_Observer);
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Creation restricted
     */
    public function testRestrictEntityCreationRestricted()
    {
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $user->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('data_object' => $user))));
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $this->_limitation->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Creation restricted'));
        $this->_model->restrictEntityCreation($observer);
    }

    public function testRestrictEntityCreationNonRestricted()
    {
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $user->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('data_object' => $user))));
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(false));
        $this->_model->restrictEntityCreation($observer);
    }

    public function testRestrictEntityCreationUpdate()
    {
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $user->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(false));
        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('data_object' => $user))));
        $this->_limitation->expects($this->never())
            ->method('isCreateRestricted');
        $this->_model->restrictEntityCreation($observer);
    }
}

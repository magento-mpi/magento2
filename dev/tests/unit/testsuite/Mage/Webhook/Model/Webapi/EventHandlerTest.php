<?php
/**
 * Mage_Webhook_Model_Webapi_EventHandler
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Webapi_EventHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Webapi_EventHandler */
    protected $_eventHandler;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_collection;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_resourceAclUser;

    public function setUp()
    {
        $this->_collection = $this->getMockBuilder('Mage_Webhook_Model_Resource_Subscription_Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_resourceAclUser = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_eventHandler = new Mage_Webhook_Model_Webapi_EventHandler(
            $this->_collection,
            $this->_resourceAclUser
        );
    }

    public function testUserChanged()
    {
        $subscription = $this->_createMockSubscription();
        $this->_setMockSubscriptions($subscription);
        $user = $this->_createMockUser(1);

        $this->_eventHandler->userChanged($user);
    }

    public function testUserChanged_noSubscription()
    {
        $this->_setMockSubscriptions(array());
        $user = $this->_createMockUser(1);

        $this->_eventHandler->userChanged($user);
    }

    public function testRoleChanged()
    {
        $subscription = $this->_createMockSubscription();
        $this->_setMockSubscriptions($subscription);
        $roleId = 42;
        $role = $this->_createMockRole($roleId);
        $users = array($this->_createMockUser(2));
        $this->_setRoleUsersExpectation($users, $roleId);

        $this->_eventHandler->roleChanged($role);
    }

    public function testRoleChanged_twoUsers()
    {
        $subscription = $this->_createMockSubscription();
        $this->_setMockSubscriptions($subscription);
        $roleId = 42;
        $role = $this->_createMockRole($roleId);
        $users = array($this->_createMockUser(1), $this->_createMockUser(2));
        $this->_setRoleUsersExpectation($users, $roleId);

        $this->_eventHandler->roleChanged($role);
    }

    public function testRoleChanged_twoSubscriptions()
    {
        $subscriptions = array($this->_createMockSubscription(), $this->_createMockSubscription());
        $this->_setMockSubscriptions($subscriptions);
        $roleId = 42;
        $role = $this->_createMockRole($roleId);
        $users = array($this->_createMockUser(1));
        $this->_setRoleUsersExpectation($users, $roleId);

        $this->_eventHandler->roleChanged($role);
    }


    public function testTopicsNoLongerValid()
    {
        $subscription = $this->_createMockSubscription();
        $subscription->expects($this->once())
            ->method('findRestrictedTopics')
            ->will($this->returnValue(array('invalid/topic')));
        $subscription->expects($this->once())
            ->method('deactivate');
        $this->_setMockSubscriptions($subscription);
        $roleId = 1;
        $role = $this->_createMockRole($roleId);
        $users = array($this->_createMockUser(2));
        $this->_setRoleUsersExpectation($users, $roleId);

        $this->_eventHandler->roleChanged($role);
    }

    protected function _setRoleUsersExpectation($users, $roleId)
    {
        $this->_resourceAclUser->expects($this->atLeastOnce())
            ->method('getRoleUsers')
            ->with($roleId)
            ->will($this->returnValue($users));
    }

    protected function _createMockRole($roleId)
    {
        $role = $this->getMockBuilder('Mage_Webapi_Model_Acl_Role')
            ->disableOriginalConstructor()
            ->getMock();
        $role->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($roleId));
        return $role;
    }

    protected function _createMockUser($userId)
    {
        $user = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->getMock();
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($userId));
        return $user;
    }

    protected function _createMockSubscription()
    {
        $subscription = $this->getMockBuilder('Mage_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();

        $subscription->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        return $subscription;
    }

    protected function _setMockSubscriptions($subscriptions)
    {
        if (!is_array($subscriptions)) {
            $subscriptions = array($subscriptions);
        }

        $this->_collection->expects($this->once())
            ->method('getApiUserSubscriptions')
            ->will($this->returnValue($subscriptions));
    }
}

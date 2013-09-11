<?php
/**
 * \Magento\Webhook\Model\Webapi\EventHandler
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Webapi_EventHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\Webapi\EventHandler */
    protected $_eventHandler;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_collection;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_resourceAclUser;

    public function setUp()
    {
        $this->_collection = $this->getMockBuilder('Magento\Webhook\Model\Resource\Subscription\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_resourceAclUser = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_eventHandler = new \Magento\Webhook\Model\Webapi\EventHandler(
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
        $role = $this->getMockBuilder('Magento\Webapi\Model\Acl\Role')
            ->disableOriginalConstructor()
            ->getMock();
        $role->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($roleId));
        return $role;
    }

    protected function _createMockUser($userId)
    {
        $user = $this->getMockBuilder('Magento\Webapi\Model\Acl\User')
            ->disableOriginalConstructor()
            ->getMock();
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($userId));
        return $user;
    }

    protected function _createMockSubscription()
    {
        $subscription = $this->getMockBuilder('Magento\Webhook\Model\Subscription')
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

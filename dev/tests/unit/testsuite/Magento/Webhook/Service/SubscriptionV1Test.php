<?php
/**
 * Magento_Webhook_Service_SubscriptionV1
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Service_SubscriptionV1Test extends PHPUnit_Framework_TestCase
{
    const VALUE_SUBSCRIPTION_ID = 2;
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_userFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionSet;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_translator;

    /** @var Magento_Webhook_Service_SubscriptionV1 */
    private $_service;

    /** @var array */
    private $_subscriptionData;

    public function setUp()
    {
        $this->_subscriptionFactory = $this->getMockBuilder('Magento_Webhook_Model_Subscription_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriptionMock = $this->getMockBuilder('Magento_Webhook_Model_Subscription')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriptionMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_SUBSCRIPTION_ID));

        $this->_subscriptionData = array(
            'name'      => 'Subscription Name',
            'alias'     => 'sub_alias',
            'topics'    => array('some/topic'),
            'subscription_id'   => self::VALUE_SUBSCRIPTION_ID,
        );
        $this->_subscriptionMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_subscriptionData));

        $this->_subscriptionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_subscriptionMock));
        $this->_subscriptionFactory->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_subscriptionMock));

        $this->_userFactory = $this->getMockBuilder('Magento_Webhook_Model_User_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriptionSet = $this->getMockBuilder('Magento_Webhook_Model_Resource_Subscription_Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_translator = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_translator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(
                function ($arr) {
                    return $arr[0];
                }
            ));

        $this->_service = new Magento_Webhook_Service_SubscriptionV1(
            $this->_subscriptionFactory,
            $this->_userFactory,
            $this->_subscriptionSet,
            $this->_translator
        );
    }

    public function testCreate()
    {
        $this->_mockAllowedTopics();

        $this->_subscriptionMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $resultData = $this->_service->create($this->_subscriptionData);

        $this->assertSame($this->_subscriptionData, $resultData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage topics
     */
    public function testCreateInvalidTopics()
    {
        $this->_mockRestrictedTopics();
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $this->_service->create($this->_subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testCreateException()
    {
        $this->_mockAllowedTopics();

        $this->_subscriptionMock->expects($this->once())
            ->method('save')
            ->will($this->throwException(new Exception()));

        $this->_service->create($this->_subscriptionData);
    }

    public function testGetAll()
    {
        $apiUserId = 42;
        $this->_subscriptionSet->expects($this->once())
            ->method('getApiUserSubscriptions')
            ->with($apiUserId)
            ->will($this->returnValue(array($this->_subscriptionMock)));
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $subscriptions = $this->_service->getAll($apiUserId);

        $this->assertEquals($this->_subscriptionData, $subscriptions[0]);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testGetAllInvalidUser()
    {
        $apiUserId = 42;
        $this->_subscriptionSet->expects($this->once())
            ->method('getApiUserSubscriptions')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $this->_service->getAll($apiUserId);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testGetAllException()
    {
        $apiUserId = 42;
        $this->_subscriptionSet->expects($this->once())
            ->method('getApiUserSubscriptions')
            ->will($this->throwException(new Exception()));
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $this->_service->getAll($apiUserId);
    }

    public function testUpdate()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $subscriptionData = $this->_service->update($this->_subscriptionData);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testUpdateFailed()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->once())
            ->method('save')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));

        $this->_service->update($this->_subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testUpdateException()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->once())
            ->method('save')
            ->will($this->throwException(new Exception()));

        $this->_service->update($this->_subscriptionData);
    }

    public function testGet()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $subscriptionData = $this->_service->get(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testGetFailed()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $subscriptionData = $this->_service->get(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testGetException()
    {
        $this->_subscriptionFactory->expects($this->any())
            ->method('create')
            ->will($this->throwException(new Exception()));
        $this->_subscriptionMock->expects($this->never())
            ->method('save');

        $subscriptionData = $this->_service->get(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    public function testDelete()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->once())
            ->method('delete')
            ->will($this->returnSelf());

        $subscriptionData = $this->_service->delete(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testDeleteFailed()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));

        $this->_service->delete(self::VALUE_SUBSCRIPTION_ID);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testDeleteException()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_subscriptionMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new Exception()));

        $this->_service->delete(self::VALUE_SUBSCRIPTION_ID);
    }

    public function testActivate()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('activate');

        $this->_subscriptionMock->expects($this->once())
            ->method('save');

        $subscriptionData = $this->_service->activate(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testActivateFailure()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('activate')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));

        $this->_service->activate(self::VALUE_SUBSCRIPTION_ID);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testActivateException()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('activate')
            ->will($this->throwException(new Exception()));

        $this->_service->activate(self::VALUE_SUBSCRIPTION_ID);
    }

    public function testDeactivate()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('deactivate');

        $this->_subscriptionMock->expects($this->once())
            ->method('save');

        $subscriptionData = $this->_service->deactivate(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testDeactivateFailure()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('deactivate')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));

        $this->_service->deactivate(self::VALUE_SUBSCRIPTION_ID);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testDeactivateException()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('deactivate')
            ->will($this->throwException(new Exception()));

        $this->_service->deactivate(self::VALUE_SUBSCRIPTION_ID);
    }

    public function testRevoke()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('revoke');

        $this->_subscriptionMock->expects($this->once())
            ->method('save');

        $subscriptionData = $this->_service->revoke(self::VALUE_SUBSCRIPTION_ID);

        $this->assertEquals($this->_subscriptionData, $subscriptionData);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage verifiable_message
     */
    public function testRevokeFailure()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('revoke')
            ->will($this->throwException(new Magento_Core_Exception('verifiable_message')));

        $this->_service->revoke(self::VALUE_SUBSCRIPTION_ID);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testRevokeException()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $this->_subscriptionMock->expects($this->once())
            ->method('revoke')
            ->will($this->throwException(new Exception()));

        $this->_service->revoke(self::VALUE_SUBSCRIPTION_ID);
    }

    public function testValidateOwnership()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $apiUserId = 42;
        $this->_subscriptionMock->expects($this->once())
            ->method('getApiUserId')
            ->will($this->returnValue($apiUserId));

        $this->_service->validateOwnership($apiUserId, self::VALUE_SUBSCRIPTION_ID);

        // validate no exception is thrown
    }

    /**
     * @expectedException Magento_Webhook_Exception
     * @expectedExceptionMessage permission
     */
    public function testValidateOwnershipFailed()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());

        $apiUserId = 42;
        $this->_subscriptionMock->expects($this->once())
            ->method('getApiUserId')
            ->will($this->returnValue(0));

        $this->_service->validateOwnership($apiUserId, self::VALUE_SUBSCRIPTION_ID);
    }


    /**
     * Mocks subscription not finding any restricted topics
     */
    private function _mockAllowedTopics()
    {
        $this->_subscriptionMock->expects($this->any())
            ->method('findRestrictedTopics')
            ->will($this->returnValue(array()));
    }

    /**
     * Mocks subscription finding restricted topics
     */
    private function _mockRestrictedTopics()
    {
        $this->_subscriptionMock->expects($this->once())
            ->method('findRestrictedTopics')
            ->will($this->returnValue(array('something/invalid')));
    }
}

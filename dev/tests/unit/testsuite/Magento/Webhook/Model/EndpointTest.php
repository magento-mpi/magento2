<?php
/**
 * Magento_Webhook_Model_Endpoint
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_EndpointTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockObjectManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockUserFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockContext;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Webhook_Model_Endpoint */
    protected $_endpoint;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockResourceEndpnt;

    public function setUp()
    {
        $this->_mockResourceEndpnt = $this->getMockBuilder('Magento_Webhook_Model_Resource_Endpoint')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockUserFactory = $this->getMockBuilder('Magento_Webhook_Model_User_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockContext = $this->getMockBuilder('Magento_Core_Model_Context')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetters()
    {
        $endpointUrl = 'https://endpoint_url';
        $timeoutInSeconds = '357';
        $format = 'presumambly_json';
        $authenticationType = 'hmac';
        $apiUsedId = '747';

        $mockWebhookUser = $this->getMockBuilder('Magento_Webhook_Model_User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockUserFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo($apiUsedId))
            ->will($this->returnValue($mockWebhookUser));

        // we have to use a mock because ancestor code utilizes deprecated static methods
        $this->_endpoint = $this->getMockBuilder('Magento_Webhook_Model_Endpoint')
            ->setConstructorArgs(
                array($this->_mockUserFactory,
                      $this->_mockContext,
                      null,
                      null)
            )
            ->setMethods(array('_init'))
            ->getMock();

        $this->_endpoint->setEndpointUrl($endpointUrl)
            ->setTimeoutInSecs($timeoutInSeconds)
            ->setFormat($format)
            ->setAuthenticationType($authenticationType)
            ->setApiUserId($apiUsedId);

        $this->assertSame($endpointUrl, $this->_endpoint->getEndpointUrl());
        $this->assertSame($timeoutInSeconds, $this->_endpoint->getTimeoutInSecs());
        $this->assertSame($format, $this->_endpoint->getFormat());
        $this->assertSame($authenticationType, $this->_endpoint->getAuthenticationType());
        $this->assertSame($mockWebhookUser, $this->_endpoint->getUser());
    }

    /**
     * Generates all possible combinations of two boolean values
     *
     * @return array of arrays of booleans
     */
    public function testBeforeSaveDataProvider()
    {
        return array(
            array(false, false),
            array(false, true),
            array(true, false),
            array(true, true)
        );
    }

    /**
     * @dataProvider testBeforeSaveDataProvider
     *
     * @param $hasAuthType
     * @param $hasDataChanges
     */
    public function testBeforeSave($hasAuthType, $hasDataChanges)
    {
        $mockEventManager = $this->getMockBuilder('Magento_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockContext->expects($this->once())
            ->method('getEventDispatcher')
            ->will($this->returnValue($mockEventManager));

        // we have to use a mock because ancestor code utilizes deprecated static methods
        $this->_endpoint = $this->getMockBuilder('Magento_Webhook_Model_Endpoint')
            ->setConstructorArgs(array($this->_mockUserFactory, $this->_mockContext))
            ->setMethods(
                array('_init', '_getResource', 'hasAuthenticationType', 'setAuthenticationType', 'setUpdatedAt',
                      'isDeleted', '_hasModelChanged')
            )
            ->getMock();

        $this->_mockMethodsForSaveCall();

        $this->_endpoint->expects($this->once())
            ->method('hasAuthenticationType')
            ->will($this->returnValue($hasAuthType));

        if (!$hasAuthType) {
            $this->_endpoint->expects($this->once())
                ->method('setAuthenticationType')
                ->with($this->equalTo(Magento_Outbound_EndpointInterface::AUTH_TYPE_NONE));
        } else {
            $this->_endpoint->expects($this->never())
                ->method('setAuthenticationType');
        }

        $this->_endpoint->setDataChanges($hasDataChanges);

        if ($hasDataChanges) {
            $someFormattedTime = '2013-07-10 12:35:28';
            $this->_mockResourceEndpnt->expects($this->once())
                ->method('formatDate')
                ->withAnyParameters() // impossible to predict what time() will be
                ->will($this->returnValue($someFormattedTime));
            $this->_endpoint->expects($this->once())
                ->method('setUpdatedAt')
                ->with($this->equalTo($someFormattedTime));
        } else {
            $this->_endpoint->expects($this->never())
                ->method('setUpdatedAt');
        }

        $this->assertSame($this->_endpoint, $this->_endpoint->save());
    }

    /**
     * This mocks the methods called in the save() method such that beforeSave()
     * will be called and no errors will be produced during the save() call
     * See Magento_Core_Model_Abstract::save() for details
     */
    private function _mockMethodsForSaveCall()
    {
        $this->_endpoint->expects($this->any())
            ->method('isDeleted')
            ->will($this->returnValue(false));

        $this->_endpoint->expects($this->any())
            ->method('_hasModelChanged')
            ->will($this->returnValue(true));

        $this->_endpoint->expects($this->any())
            ->method('_getResource')
            ->will($this->returnValue($this->_mockResourceEndpnt));

        $mockResourceAbstract = $this->getMockBuilder('Magento_webhook_Model_Resource_Endpoint')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockResourceEndpnt->expects($this->any())
            ->method('addCommitCallback')
            ->withAnyParameters()
            ->will($this->returnValue($mockResourceAbstract));
    }
}

<?php
/**
 * \Magento\Outbound\Message\Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Outbound\Message;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    const ENDPOINT_URL = 'https://endpoint_url';

    const TOPIC = 'topic';

    const CONTENT_TYPE = 'content_type';

    const AUTH_TYPE = 'auth_type';

    const FORMATTED_BODY = 'some_formatted_body';

    const TIMEOUT = 777;

    public static $body = array('some_body');

    public static $signatureHeaders = array('signature' => 'hash');

    /** @var \Magento\Outbound\Message\Factory */
    protected $_factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockObjectManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockFormatFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockFormatter;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAuthFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockEndpoint;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->_mockFormatFactory = $this->getMockBuilder('Magento\Outbound\Formatter\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAuthFactory = $this->getMockBuilder('Magento\Outbound\Authentication\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_factory = new \Magento\Outbound\Message\Factory($this->_mockObjectManager,
                                                               $this->_mockFormatFactory,
                                                               $this->_mockAuthFactory);

        $this->_mockFormatter = $this->getMockBuilder('Magento\Outbound\FormatterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockEndpoint = $this->getMockBuilder('Magento\Outbound\EndpointInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockEndpoint->expects($this->once())
            ->method('getFormat')
            ->will($this->returnValue('some_format'));

        $this->_mockFormatFactory->expects($this->once())
            ->method('getFormatter')
            ->with($this->equalTo('some_format'))
            ->will($this->returnValue($this->_mockFormatter));

        $this->_mockFormatter->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue(self::CONTENT_TYPE));

        $this->_mockFormatter->expects($this->once())
            ->method('format')
            ->with($this->equalTo(self::$body))
            ->will($this->returnValue(self::FORMATTED_BODY));

        $this->_mockEndpoint->expects($this->once())
            ->method('getAuthenticationType')
            ->will($this->returnValue(self::AUTH_TYPE));

        $mockAuth = $this->getMockBuilder('Magento\Outbound\AuthenticationInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAuthFactory->expects($this->once())
            ->method('getAuthentication')
            ->with($this->equalTo(self::AUTH_TYPE))
            ->will($this->returnValue($mockAuth));

        $mockUser = $this->getMockBuilder('Magento\Outbound\UserInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockEndpoint->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($mockUser));

        $mockAuth->expects($this->once())
            ->method('getSignatureHeaders')
            ->with($this->equalTo(self::FORMATTED_BODY), $this->equalTo($mockUser))
            ->will($this->returnValue(self::$signatureHeaders));

        $this->_mockEndpoint->expects($this->once())
            ->method('getEndpointUrl')
            ->will($this->returnValue(self::ENDPOINT_URL));

        $this->_mockEndpoint->expects($this->once())
            ->method('getTimeoutInSecs')
            ->will($this->returnValue(self::TIMEOUT));

        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Outbound\Message'), $this->anything())
            ->will($this->returnCallback(array($this, 'verifyManagerCreate')));
    }

    public function testCreate()
    {
        $this->assertEquals('SUCCESS', $this->_factory->create($this->_mockEndpoint, self::TOPIC, self::$body));
    }

    /**
     * Used to verify the correct arguments are passed in to \Magento\ObjectManager::create
     *
     * @param       $className
     * @param array $arguments
     *
     * @return string
     */
    public function verifyManagerCreate($className, array $arguments)
    {
        $this->assertSame('Magento\Outbound\Message', $className);

        $this->assertCount(4, $arguments);

        $this->assertArrayHasKey('endpointUrl', $arguments);
        $this->assertSame(self::ENDPOINT_URL, $arguments['endpointUrl']);

        $this->assertArrayHasKey('headers', $arguments);
        $headers = $arguments['headers'];
        $this->assertArrayHasKey(\Magento\Outbound\Message\FactoryInterface::TOPIC_HEADER, $headers);
        $this->assertSame(self::TOPIC, $headers[\Magento\Outbound\Message\FactoryInterface::TOPIC_HEADER]);
        $this->assertArrayHasKey(\Magento\Outbound\FormatterInterface::CONTENT_TYPE_HEADER, $headers);
        $this->assertSame(self::CONTENT_TYPE, $headers[\Magento\Outbound\FormatterInterface::CONTENT_TYPE_HEADER]);
        foreach (self::$signatureHeaders as $key => $value) {
            $this->assertArrayHasKey($key, $headers);
            $this->assertSame($value, $headers[$key]);
        }

        $this->assertArrayHasKey('body', $arguments);
        $this->assertSame(self::FORMATTED_BODY, $arguments['body']);

        $this->assertArrayHasKey('timeout', $arguments);
        $this->assertSame(self::TIMEOUT, $arguments['timeout']);

        return 'SUCCESS';
    }
}

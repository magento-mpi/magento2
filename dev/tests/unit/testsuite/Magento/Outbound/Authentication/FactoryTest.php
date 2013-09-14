<?php
/**
 * \Magento\Outbound\Authentication\Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Authentication_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockObjectManager;

    /**
     * @var \Magento\Outbound\Authentication\Factory
     */
    protected $_authFactory;

    /**
     * @var \Magento\Outbound\Authentication\Hmac
     */
    protected $_expectedObject;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->_authFactory = new \Magento\Outbound\Authentication\Factory(array('hmac' => 'Test_Authentication_Hmac'),
            $this->_mockObjectManager);

        $this->_expectedObject = $this->getMockBuilder('Magento\Outbound\Authentication\Hmac')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_formatterFactory = new \Magento\Outbound\Formatter\Factory(
            array('json' => 'Test_Formatter_Json'),
            $this->_mockObjectManager
        );
    }

    public function testGetAuthenticationSuccess()
    {
        $this->_mockObjectManager->expects($this->once())
            ->method('get')
            ->with('Test_Authentication_Hmac')
            ->will($this->returnValue($this->_expectedObject));

        $authObject = $this->_authFactory->getAuthentication(\Magento\Outbound\EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertInstanceOf('Magento\Outbound\Authentication\Hmac', $authObject);
        $this->assertEquals($this->_expectedObject, $authObject);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage There is no authentication for the type given: TEST_AUTH_TYPE_STRING
     */
    public function testGetAuthenticationNoType()
    {
        $this->_authFactory->getAuthentication('TEST_AUTH_TYPE_STRING');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Authentication class for hmac does not implement authentication interface
     */
    public function testGetAuthenticationNoModel()
    {
        $this->_mockObjectManager->expects($this->once())
            ->method('get')
            ->with('Test_Authentication_Hmac')
            ->will($this->returnValue($this->getMock('Magento\Object')));
        $this->_authFactory->getAuthentication(\Magento\Outbound\EndpointInterface::AUTH_TYPE_HMAC);
    }
}

<?php
/**
 * Magento_Outbound_Authentication_Hmac
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Authentication_HmacTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Magento_Outbound_Authentication_Hmac
     */
    private $_model;

    /**
     * A random 32 byte string
     */
    const SHARED_SECRET = 'x0lpu8kcmu23l8jcqd7qmyknyl5kx2f9';

    /** message body */
    const BODY = 'This is a test body and has no semantic value.';

    /** message head domain */
    const DOMAIN = 'www.fake.magento.com';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject Magento_Outbound_MessageInterface
     */
    private $_mockMessage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject Magento_Outbound_UserInterface
     */
    private $_mockUser;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject Magento_Core_Model_StoreManagerInterface
     */
    private $_mockStoreManager;

    public function setUp()
    {
        $this->_mockStoreManager = $this->getMockBuilder('Magento_Core_Model_StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = new Magento_Outbound_Authentication_Hmac($this->_mockStoreManager);

        $this->_mockMessage = $this->getMockBuilder('Magento_Outbound_MessageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockMessage->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue(self::BODY));

        $this->_mockUser = $this->getMockBuilder('Magento_Outbound_UserInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHeaders()
    {
        $store = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockStoreManager->expects($this->once())
            ->method('getSafeStore')
            ->will($this->returnValue($store));
        $store->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://' . self::DOMAIN));
        
        $this->_mockUser->expects($this->once())
            ->method('getSharedSecret')
            ->will($this->returnValue(self::SHARED_SECRET));

        $hash = (string) hash_hmac(
            Magento_Outbound_Authentication_Hmac::SHA256_ALGORITHM,
            self::BODY,
            self::SHARED_SECRET
        );

        $headers = $this->_model->getSignatureHeaders($this->_mockMessage->getBody(), $this->_mockUser);
        $this->assertArrayHasKey(Magento_Outbound_Authentication_Hmac::DOMAIN_HEADER, $headers);
        $this->assertSame(self::DOMAIN, $headers[Magento_Outbound_Authentication_Hmac::DOMAIN_HEADER]);
        $this->assertArrayHasKey(Magento_Outbound_Authentication_Hmac::HMAC_HEADER, $headers);
        $this->assertSame($hash, $headers[Magento_Outbound_Authentication_Hmac::HMAC_HEADER]);

    }

    /**
     * @expectedException LogicException
     * @expectedMessage The shared secret cannot be a empty.
     */
    public function testEmptySecret()
    {
        $this->_mockUser->expects($this->once())
            ->method('getSharedSecret')
            ->will($this->returnValue(''));

        $this->_model->getSignatureHeaders($this->_mockMessage, $this->_mockUser);
    }
}

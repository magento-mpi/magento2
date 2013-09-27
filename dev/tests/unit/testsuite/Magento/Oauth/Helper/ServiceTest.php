<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Oauth_Helper_ServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Helper_Data */
    protected $_coreHelper;

    /** @var Magento_Core_Helper_Context */
    protected $_coreContextMock;

    /** @var Magento_Core_Model_Store_Config */
    protected $_storeConfigMock;

    /** @var Magento_Oauth_Helper_Service */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_coreContextMock = $this->getMockBuilder('Magento_Core_Helper_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeConfigMock = $this->getMockBuilder('Magento_Core_Model_Store_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_coreHelper = new Magento_Core_Helper_Data(
            $this->getMockBuilder('Magento_Core_Model_Event_Manager')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento_Core_Helper_Http')->disableOriginalConstructor()->getMock(),
            $this->_coreContextMock,
            $this->getMockBuilder('Magento_Core_Model_Config')->disableOriginalConstructor()->getMock(),
            $this->_storeConfigMock
        );

        $this->_oauthHelper = new Magento_Oauth_Helper_Service(
            $this->_coreHelper,
            $this->_coreContextMock,
            $this->_storeConfigMock
        );
    }

    protected function tearDown()
    {
        unset($this->_coreHelper);
        unset($this->_coreContextMock);
        unset($this->_storeConfigMock);
        unset($this->_oauthHelper);
    }

    public function testGenerateToken()
    {
        $token = $this->_oauthHelper->generateToken();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Token::LENGTH_TOKEN);
    }

    public function testGenerateTokenSecret()
    {
        $token = $this->_oauthHelper->generateTokenSecret();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Token::LENGTH_SECRET);
    }

    public function testGenerateVerifier()
    {
        $token = $this->_oauthHelper->generateVerifier();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Token::LENGTH_VERIFIER);
    }

    public function testGenerateConsumerKey()
    {
        $token = $this->_oauthHelper->generateConsumerKey();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Consumer::KEY_LENGTH);
    }

    public function testGenerateConsumerSecret()
    {
        $token = $this->_oauthHelper->generateConsumerSecret();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Consumer::SECRET_LENGTH);
    }

    public function testIsCleanupProbabilityZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertFalse($this->_oauthHelper->isCleanupProbability());
    }

    public function testIsCleanupProbabilityRandomOne()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(1));
        $this->assertTrue($this->_oauthHelper->isCleanupProbability());
    }

    public function testGetCleanupExpirationPeriodZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertEquals(
            Magento_Oauth_Helper_Service::CLEANUP_EXPIRATION_PERIOD_DEFAULT,
            $this->_oauthHelper->getCleanupExpirationPeriod()
        );
    }

    public function testGetCleanupExpirationPeriodNonZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->_oauthHelper->getCleanupExpirationPeriod());
    }

    public function testGetConsumerExpirationPeriodZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertEquals(
            Magento_Oauth_Helper_Service::CONSUMER_EXPIRATION_PERIOD_DEFAULT,
            $this->_oauthHelper->getConsumerExpirationPeriod()
        );
    }

    public function testGetConsumerExpirationPeriodNonZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->_oauthHelper->getConsumerExpirationPeriod());
    }
}

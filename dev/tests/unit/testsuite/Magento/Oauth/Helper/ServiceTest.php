<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Oauth\Helper;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Helper\Context */
    protected $_coreContextMock;

    /** @var \Magento\Core\Model\Store\Config */
    protected $_storeConfigMock;

    /** @var \Magento\Oauth\Helper\Service */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_coreContextMock = $this->getMockBuilder('Magento\Core\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeConfigMock = $this->getMockBuilder('Magento\Core\Model\Store\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_coreHelper = new \Magento\Core\Helper\Data(
            $this->_coreContextMock,
            $this->getMockBuilder('Magento\Core\Model\Config')->disableOriginalConstructor()->getMock(),
            $this->_storeConfigMock,
            $this->getMockBuilder('Magento\Core\Model\StoreManager')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Locale')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Date')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\App\State')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Object\Copy')->disableOriginalConstructor()->getMock()
        );

        $this->_oauthHelper = new \Magento\Oauth\Helper\Service(
            $this->_coreContextMock,
            $this->_storeConfigMock,
            new \Magento\Math\Random
        );
    }

    protected function tearDown()
    {
        unset($this->_coreContextMock);
        unset($this->_storeConfigMock);
        unset($this->_oauthHelper);
    }

    public function testGenerateToken()
    {
        $token = $this->_oauthHelper->generateToken();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Model\Token::LENGTH_TOKEN);
    }

    public function testGenerateTokenSecret()
    {
        $token = $this->_oauthHelper->generateTokenSecret();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Model\Token::LENGTH_SECRET);
    }

    public function testGenerateVerifier()
    {
        $token = $this->_oauthHelper->generateVerifier();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Model\Token::LENGTH_VERIFIER);
    }

    public function testGenerateConsumerKey()
    {
        $token = $this->_oauthHelper->generateConsumerKey();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Model\Consumer::KEY_LENGTH);
    }

    public function testGenerateConsumerSecret()
    {
        $token = $this->_oauthHelper->generateConsumerSecret();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Model\Consumer::SECRET_LENGTH);
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
            \Magento\Oauth\Helper\Service::CLEANUP_EXPIRATION_PERIOD_DEFAULT,
            $this->_oauthHelper->getCleanupExpirationPeriod()
        );
    }

    public function testGetCleanupExpirationPeriodNonZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->_oauthHelper->getCleanupExpirationPeriod());
    }
}

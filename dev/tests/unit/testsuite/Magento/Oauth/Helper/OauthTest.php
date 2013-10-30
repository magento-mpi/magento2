<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Oauth\Helper;

class OauthTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Helper\Data */
    protected $_coreHelper;

    /** @var \Magento\Oauth\Helper\Oauth */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_coreHelper = new \Magento\Core\Helper\Data(
            $this->getMockBuilder('Magento\Core\Helper\Context')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Event\Manager')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Helper\Http')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Config')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Store\Config')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\StoreManager')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Locale')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Date')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\App\State')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento\Core\Model\Encryption')->disableOriginalConstructor()->getMock()
        );

        $this->_oauthHelper = new \Magento\Oauth\Helper\Oauth($this->_coreHelper);
    }

    protected function tearDown()
    {
        unset($this->_coreHelper);
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
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Helper\Oauth;

class OauthTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Oauth\Helper\Oauth */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_oauthHelper = new \Magento\Oauth\Helper\Oauth(new \Magento\Math\Random());
    }

    protected function tearDown()
    {
        unset($this->_oauthHelper);
    }

    public function testGenerateToken()
    {
        $token = $this->_oauthHelper->generateToken();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Helper\Oauth::LENGTH_TOKEN);
    }

    public function testGenerateTokenSecret()
    {
        $token = $this->_oauthHelper->generateTokenSecret();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Helper\Oauth::LENGTH_TOKEN_SECRET);
    }

    public function testGenerateVerifier()
    {
        $token = $this->_oauthHelper->generateVerifier();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Helper\Oauth::LENGTH_TOKEN_VERIFIER);
    }

    public function testGenerateConsumerKey()
    {
        $token = $this->_oauthHelper->generateConsumerKey();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Helper\Oauth::LENGTH_CONSUMER_KEY);
    }

    public function testGenerateConsumerSecret()
    {
        $token = $this->_oauthHelper->generateConsumerSecret();
        $this->assertTrue(is_string($token) && strlen($token) === \Magento\Oauth\Helper\Oauth::LENGTH_CONSUMER_SECRET);
    }
}

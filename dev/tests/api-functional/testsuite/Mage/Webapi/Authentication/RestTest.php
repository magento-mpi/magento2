<?php
require_once __DIR__ . '/../../../../lib/OAuth/bootstrap.php';

/**
 * Test authentication mechanisms in REST.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Authentication_RestTest extends Magento_Test_TestCase_WebapiAbstract
{
    /** @var Mage_Webapi_Authentication_Rest_OauthClient[] */
    protected $_oAuthClients = array();

    protected function setUp()
    {
        // TODO: Uncomment tests
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");

        $this->_markTestAsRestOnly();
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->_oAuthClients = array();
        parent::tearDown();
    }

    public function testGetRequestToken()
    {
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), $this->_getValidConsumerSecretA());
        $requestToken = $oAuthClient->requestRequestToken();

        $this->assertNotEmpty($requestToken->getRequestToken(), "Request token value is not set");
        $this->assertNotEmpty($requestToken->getRequestTokenSecret(), "Request token secret is not set");

        $this->assertNotEmpty($oAuthClient->getOauthVerifier(), "Oauth verifier is empty.");
    }

    public function testGetRequestTokenInvalidConsumerKey()
    {
        $oAuthClient = $this->_getOauthClient('invalid_key', '');
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestRequestToken();
    }

    public function testGetRequestTokenInvalidConsumerSecret()
    {
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), 'invalid_secret');
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestRequestToken();
    }

    /**
     * @depends testGetRequestToken
     */
    public function testGetAccessToken()
    {
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), $this->_getValidConsumerSecretA());
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            $oAuthClient->getOauthVerifier(),
            $requestToken->getRequestTokenSecret()
        );
        $this->assertNotEmpty($accessToken->getAccessToken(), "Access token value is not set.");
        $this->assertNotEmpty($accessToken->getAccessTokenSecret(), "Access token secret is not set.");
    }

    /**
     * @depends testGetRequestToken
     */
    public function testGetAccessTokenInvalidConsumerKey()
    {
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), $this->_getValidConsumerSecretA());
        $requestToken = $oAuthClient->requestRequestToken();
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestAccessToken(
            'invalid_key',
            $oAuthClient->getOauthVerifier(),
            $requestToken->getRequestTokenSecret()
        );
    }

    /**
     * @depends testGetRequestToken
     */
    public function testGetAccessTokenInvalidConsumerSecret()
    {
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), $this->_getValidConsumerSecretA());
        $requestToken = $oAuthClient->requestRequestToken();
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            $oAuthClient->getOauthVerifier(),
            'invalid_secret'
        );
    }

    /**
     * @depends testGetRequestToken
     */
    public function testGetAccessTokenInvalidVerifier()
    {
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), $this->_getValidConsumerSecretA());
        $requestToken = $oAuthClient->requestRequestToken();
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            'invalid_verifier',
            $requestToken->getRequestTokenSecret()
        );
    }

    /**
     * @depends testGetRequestToken
     */
    public function testGetAccessTokenConsumerMismatch()
    {
        $oAuthClientA = $this->_getOauthClient($this->_getValidConsumerKeyA(), $this->_getValidConsumerSecretA());
        $requestTokenA = $oAuthClientA->requestRequestToken();
        $oAuthClientB = $this->_getOauthClient($this->_getValidConsumerKeyB(), $this->_getValidConsumerSecretB());

        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClientB->requestAccessToken(
            $requestTokenA->getRequestToken(),
            $oAuthClientA->getOauthVerifier(),
            $requestTokenA->getRequestTokenSecret()
        );
    }

    public function testAccessApi()
    {
        // TODO: Implement
        $this->markTestIncomplete("Implement in scope of MAGETWO-11272");
    }

    public function testAccessApiInvalidCredentials()
    {
        // TODO: Implement
        $this->markTestIncomplete("Implement in scope of MAGETWO-11272");
    }

    public function testAccessApiInvalidSignature()
    {
        // TODO: Implement
        $this->markTestIncomplete("Implement in scope of MAGETWO-11272");
    }

    protected function _getOauthClient($consumerKey, $consumerSecret)
    {
        if (!isset($this->_oAuthClients[$consumerKey])) {
            $credentials = new OAuth\Common\Consumer\Credentials($consumerKey, $consumerSecret, '');
            $this->_oAuthClients[$consumerKey] = new Mage_Webapi_Authentication_Rest_OauthClient($credentials);
        }
        return $this->_oAuthClients[$consumerKey];
    }

    protected function _getValidConsumerKeyA()
    {
        /** TODO: Implement */
        return 'valid_key1';
    }

    protected function _getValidConsumerSecretA()
    {
        /** TODO: Implement */
        return 'valid_secret1';
    }

    protected function _getValidConsumerKeyB()
    {
        /** TODO: Implement */
        return 'valid_key2';
    }

    protected function _getValidConsumerSecretB()
    {
        /** TODO: Implement */
        return 'valid_secret2';
    }
}

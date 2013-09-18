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
class Magento_Webapi_Authentication_RestTest extends Magento_TestFramework_TestCase_WebapiAbstract
{
    /** @var Magento_Webapi_Authentication_Rest_OauthClient[] */
    protected $_oAuthClients = array();

    /** @var string */
    protected $_consumerKey;

    /** @var string */
    protected $_consumerSecret;

    /** @var string */
    protected $_verifier;

    protected function setUp()
    {
        $this->_markTestAsRestOnly();
        parent::setUp();
    }

    private function _runConsumerFixture(){

        $url = 'http://magento.ll';
        $this->_consumerKey = md5(rand());
        $this->_consumerSecret = md5(rand());

        /** @var  $token Magento_Oauth_Model_Consumer */
        $consumer = Mage::getModel('Magento_Oauth_Model_Consumer');
        $consumer
            ->setCreatedAt('2012-12-31 23:59:59')
            ->setUpdatedAt('2012-12-31 23:59:59')
            ->setName('consumerName')
            ->setKey($this->_consumerKey)
            ->setSecret($this->_consumerSecret)
            ->setCallbackUrl($url)
            ->setRejectedCallbackUrl($url)
            ->setHttpPostUrl($url);

        $consumer->isObjectNew(true);
        $consumer->save();

        /** @var  $token Magento_Oauth_Model_Token */
        $token = Mage::getModel('Magento_Oauth_Model_Token');
        $token->createVerifierToken($consumer->getId(), $url);

        /**
         * Verifier is created when during the consumer creation and posting the credentials
         * This is subsequently used for requesting access token
         */
        $this->_verifier = $token->getVerifier();

    }

    protected function tearDown()
    {
        $this->_oAuthClients = array();
        parent::tearDown();
    }

    /**
     * TODO: Fixture can be used now
     * @ magentoApiDataFixture Magento/Oauth/_files/consumer.php
     */
    public function testGetRequestToken()
    {
        $this->_runConsumerFixture();
        /** @var $oAuthClient Magento_Webapi_Authentication_Rest_OauthClient*/
        $oAuthClient = $this->_getOauthClient($this->_consumerKey, $this->_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();

        $this->assertNotEmpty($requestToken->getRequestToken(), "Request token value is not set");
        $this->assertNotEmpty($requestToken->getRequestTokenSecret(), "Request token secret is not set");

        //Oauth verifier is not expected for requestToken requests
        //$this->assertNotEmpty($oAuthClient->getOauthVerifier(), "Oauth verifier is empty.");
    }

    public function testGetRequestTokenInvalidConsumerKey()
    {
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");
        $oAuthClient = $this->_getOauthClient('invalid_key', '');
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestRequestToken();
    }

    public function testGetRequestTokenInvalidConsumerSecret()
    {
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");
        $oAuthClient = $this->_getOauthClient($this->_getValidConsumerKeyA(), 'invalid_secret');
        // TODO: Set proper error message
        $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException', "Error occurred: '???'");
        $oAuthClient->requestRequestToken();
    }


    public function testGetAccessToken()
    {
        $this->_runConsumerFixture();
        $oAuthClient = $this->_getOauthClient($this->_consumerKey, $this->_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            $this->_verifier,
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
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");
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
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");
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
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");
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
        $this->markTestIncomplete("Enable tests in scope of MAGETWO-11272");
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
            $credentials = new OAuth\Common\Consumer\Credentials($consumerKey, $consumerSecret, 'http://magento.ll');
            $this->_oAuthClients[$consumerKey] = new Magento_Webapi_Authentication_Rest_OauthClient($credentials);
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

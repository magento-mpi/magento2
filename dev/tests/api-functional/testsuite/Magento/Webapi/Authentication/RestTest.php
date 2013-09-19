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
    /**#@+
     * Consumer credentials used by fixture
     */
    const CONSUMER_KEY = 'ec049f278b41470dd0e9ecc9369fc327';
    const CONSUMER_SECRET = '5c7368d5679563a902701bf8b46575fc';

    /** @var Magento_Webapi_Authentication_Rest_OauthClient[] */
    protected $_oAuthClients = array();

    /** @var Magento_Oauth_Model_Consumer */
    protected $_consumer;

    /** @var Magento_Oauth_Model_Token */
    protected $_token;

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

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $this->_consumer = $objectManager->get('Magento_Oauth_Model_Consumer')->load(self::CONSUMER_KEY, 'key');

        $this->_token = $objectManager->get('Magento_Oauth_Model_Token')
            ->load($this->_consumer->getId(), 'consumer_id');

        $this->_consumerKey = $this->_consumer->getKey();
        $this->_consumerSecret = $this->_consumer->getSecret();
        /**
         * Verifier is created when during the consumer creation and posting the credentials
         * This is subsequently used for requesting access token
         */
        $this->_verifier = $this->_token->getVerifier();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->_oAuthClients = array();
        if (isset($this->_consumer)) {
            $this->_consumer->delete();
            $this->_token->delete();
        }
    }


    /**
     * @magentoApiDataFixture Magento/Oauth/_files/consumer.php
     */
    public function testGetRequestToken()
    {
        /** @var $oAuthClient Magento_Webapi_Authentication_Rest_OauthClient */
        $oAuthClient = $this->_getOauthClient($this->_consumerKey, $this->_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();

        $this->assertNotEmpty($requestToken->getRequestToken(), "Request token value is not set");
        $this->assertNotEmpty($requestToken->getRequestTokenSecret(), "Request token secret is not set");

        //Oauth verifier is not expected for requestToken requests
        //$this->assertNotEmpty($oAuthClient->getOauthVerifier(), "Oauth verifier is empty.");
    }

    public function testGetRequestTokenInvalidConsumerKey()
    {
        try {
            $oAuthClient = $this->_getOauthClient('invalid_key', $this->_consumerSecret);
            $oAuthClient->requestRequestToken();
        } catch (Exception $exception) {
            $this->assertContains('HTTP/1.1 401 Authorization Required', $exception->getMessage());
        }
    }

    public function testGetRequestTokenInvalidConsumerSecret()
    {
        try {
            $oAuthClient = $this->_getOauthClient($this->_consumerKey, 'invalid_secret');
            $oAuthClient->requestRequestToken();
        } catch (Exception $exception) {
            $this->assertContains('HTTP/1.1 401 Authorization Required', $exception->getMessage());
        }
    }


    /**
     * @magentoApiDataFixture Magento/Oauth/_files/consumer.php
     */
    public function testGetAccessToken()
    {
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
     * @magentoApiDataFixture Magento/Oauth/_files/consumer.php
     */
    public function testAccessApi()
    {
        //TODO: This is not really getting tested at this point since authn is commented in the webapi framework
        $oAuthClient = $this->_getOauthClient($this->_consumerKey, $this->_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            $this->_verifier,
            $requestToken->getRequestTokenSecret()
        );

        $responseArray = $oAuthClient->validateAccessToken($accessToken);

        $this->assertNotEmpty($responseArray);
        $this->assertEquals('testProduct2',
                            $responseArray[1]->name,
                            'Invocation to /rest/V1/testmodule1 expected to return testProduct2 but returned '
                            . $responseArray[1]->name);
    }

    /**
     * @magentoApiDataFixture Magento/Oauth/_files/consumer.php
     */
    public function testGetAccessTokenInvalidVerifier()
    {
        try {
            $oAuthClient = $this->_getOauthClient($this->_consumerKey, $this->_consumerSecret);
            $requestToken = $oAuthClient->requestRequestToken();
            $oAuthClient->requestAccessToken(
                $requestToken->getRequestToken(),
                'invalid verifier',
                $requestToken->getRequestTokenSecret()
            );
        } catch (Exception $exception) {
            $this->assertContains('HTTP/1.1 401 Authorization Required', $exception->getMessage());
        }
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

    /**
     * @magentoApiDataFixture Magento/Oauth/_files/consumer.php
     */
    public function testAccessApiInvalidAccessToken()
    {
        try {
            $oAuthClient = $this->_getOauthClient($this->_consumerKey, $this->_consumerSecret);
            $requestToken = $oAuthClient->requestRequestToken();
            $accessToken = $oAuthClient->requestAccessToken(
                $requestToken->getRequestToken(),
                $this->_verifier,
                $requestToken->getRequestTokenSecret()
            );
            $accessToken->setAccessToken('invalid');
            $oAuthClient->validateAccessToken($accessToken);

        } catch (Exception $exception) {
            //TODO : Need to update once error handling is fixed
            $this->assertContains('HTTP/1.1 400 Bad Request', $exception->getMessage());
        }
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

}

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

/**
 * @magentoApiDataFixture consumerFixture
 */
class Magento_Webapi_Authentication_RestTest extends Magento_TestFramework_TestCase_WebapiAbstract
{
    /** @var Magento_Webapi_Authentication_Rest_OauthClient[] */
    protected $_oAuthClients = array();

    /** @var Magento_Oauth_Model_Consumer */
    protected static $_consumer;

    /** @var Magento_Oauth_Model_Token */
    protected static $_token;

    /** @var string */
    protected static $_consumerKey;

    /** @var string */
    protected static $_consumerSecret;

    /** @var string */
    protected static $_verifier;

    /** @var string */
    protected $_tokenLength = Magento_Oauth_Model_Token::LENGTH_TOKEN;

    /** @var string */
    protected $_secretLength = Magento_Oauth_Model_Token::LENGTH_SECRET;


    protected function setUp()
    {
        $this->_markTestAsRestOnly();
        parent::setUp();
    }

    /**
     * Create a consumer
     */
    public static function consumerFixture($date = null)
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $oauthService Magento_Oauth_Service_OauthV1 */
        $oauthService = $objectManager->get('Magento_Oauth_Service_OauthV1');
        /** @var $oauthHelper Magento_Oauth_Helper_Data */
        $oauthHelper = $objectManager->get('Magento_Oauth_Helper_Data');

        self::$_consumerKey = $oauthHelper->generateConsumerKey();
        self::$_consumerSecret = $oauthHelper->generateConsumerSecret();

        $url = TESTS_BASE_URL;
        $data = array(
            'created_at' => is_null($date) ? date('Y-m-d H:i:s') : $date,
            'key' => self::$_consumerKey,
            'secret' => self::$_consumerSecret,
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url,
            'http_post_url' => $url
        );

        /** @var array $consumerData */
        $consumerData = $oauthService->createConsumer($data);
        /** @var  $token Magento_Oauth_Model_Token */
        self::$_consumer = $objectManager->get('Magento_Oauth_Model_Consumer')
            ->load($consumerData['key'], 'key');
        self::$_token = $objectManager->create('Magento_Oauth_Model_Token');
        self::$_verifier = self::$_token->createVerifierToken(self::$_consumer->getId())->getVerifier();
    }


    protected function tearDown()
    {
        parent::tearDown();
        $this->_oAuthClients = array();
        if (isset(self::$_consumer)) {
            self::$_consumer->delete();
            self::$_token->delete();
        }
    }

    public function testGetRequestToken()
    {
        /** @var $oAuthClient Magento_Webapi_Authentication_Rest_OauthClient */
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();

        $this->assertNotEmpty($requestToken->getRequestToken(), "Request token value is not set");
        $this->assertNotEmpty($requestToken->getRequestTokenSecret(), "Request token secret is not set");

        $this->assertNotEmpty($this->_tokenLength, strlen($requestToken->getRequestToken()),
                              "Request token value length should be " . $this->_tokenLength);
        $this->assertNotEmpty($this->_secretLength, strlen($requestToken->getRequestTokenSecret()),
                              "Request token secret length should be " . $this->_secretLength);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage HTTP/1.1 401 Authorization Required
     */
    public function testGetRequestTokenExpiredConsumer()
    {
        $this::consumerFixture('2012-01-01 00:00:00');
        /** @var $oAuthClient Magento_Webapi_Authentication_Rest_OauthClient*/
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage HTTP/1.1 401 Authorization Required
     */
    public function testGetRequestTokenInvalidConsumerKey()
    {
        $oAuthClient = $this->_getOauthClient('invalid_key', self::$_consumerSecret);
        $oAuthClient->requestRequestToken();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage HTTP/1.1 401 Authorization Required
     */
    public function testGetRequestTokenInvalidConsumerSecret()
    {
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, 'invalid_secret');
        $oAuthClient->requestRequestToken();
    }

    public function testGetAccessToken()
    {
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            self::$_verifier,
            $requestToken->getRequestTokenSecret()
        );
        $this->assertNotEmpty($accessToken->getAccessToken(), "Access token value is not set.");
        $this->assertNotEmpty($accessToken->getAccessTokenSecret(), "Access token secret is not set.");

        $this->assertNotEmpty($this->_tokenLength, strlen($accessToken->getAccessToken()),
                              "Access token value length should be " . $this->_tokenLength);
        $this->assertNotEmpty($this->_secretLength, strlen($accessToken->getAccessTokenSecret()),
                              "Access token secret length should be " . $this->_secretLength);
    }

    public function testAccessApi()
    {
        //TODO: This is not really getting tested at this point since authn is commented in the webapi framework
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            self::$_verifier,
            $requestToken->getRequestTokenSecret()
        );

        $responseArray = $oAuthClient->validateAccessToken($accessToken);

        $this->assertNotEmpty($responseArray);
        $this->assertEquals(
            'testProduct2',
            $responseArray[1]->name,
            'Invocation to /rest/V1/testmodule1 expected to return testProduct2 but returned '
            . $responseArray[1]->name);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage HTTP/1.1 401 Authorization Required
     */
    public function testGetAccessTokenInvalidVerifier()
    {
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
        $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            'invalid verifier',
            $requestToken->getRequestTokenSecret()
        );
    }

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
     * @expectedException Exception
     * @expectedExceptionMessage HTTP/1.1 400
     */
    public function testAccessApiInvalidAccessToken()
    {
        // TODO: Implement
        $this->markTestIncomplete("validateAccessToken does not produce the exception as expected");

        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();
        $accessToken = $oAuthClient->requestAccessToken(
            $requestToken->getRequestToken(),
            self::$_verifier,
            $requestToken->getRequestTokenSecret()
        );
        $accessToken->setAccessToken('invalid');
        $oAuthClient->validateAccessToken($accessToken);
    }

    public function testAccessApiInvalidSignature()
    {
        // TODO: Implement
        $this->markTestIncomplete("Implement in scope of MAGETWO-11272");
    }

    protected function _getOauthClient($consumerKey, $consumerSecret)
    {
        if (!isset($this->_oAuthClients[$consumerKey])) {
            $credentials = new OAuth\Common\Consumer\Credentials($consumerKey, $consumerSecret, TESTS_BASE_URL);
            $this->_oAuthClients[$consumerKey] = new Magento_Webapi_Authentication_Rest_OauthClient($credentials);
        }
        return $this->_oAuthClients[$consumerKey];
    }

}

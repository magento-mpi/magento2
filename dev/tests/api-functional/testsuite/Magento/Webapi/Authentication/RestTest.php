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

    protected function setUp()
    {
        $this->_markTestAsRestOnly();
        parent::setUp();
    }


    /**
     * Simple product with stock item
     */
    public static function consumerFixture()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $oauthService Magento_Oauth_Service_OauthV1 */
        $oauthService = $objectManager->get('Magento_Oauth_Service_OauthV1');
        /** @var $oauthHelper Magento_Oauth_Helper_Data */
        $oauthHelper = $objectManager->get('Magento_Oauth_Helper_Data');

        self::$_consumerKey = $oauthHelper->generateConsumerKey();
        self::$_consumerSecret = $oauthHelper->generateConsumerSecret();

        $url = 'http://magento.ll';
        $data = array(
            'key' => self::$_consumerKey,
            'secret' => self::$_consumerSecret,
            'name' => 'consumerName',
            'callback_url' => $url,
            'rejected_callback_url' => $url,
            'http_post_url' => $url
        );
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

    /**
     * @magentoApiDataFixture consumerFixture
     */
    public function testGetRequestToken()
    {
        /** @var $oAuthClient Magento_Webapi_Authentication_Rest_OauthClient */
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();

        $this->assertNotEmpty($requestToken->getRequestToken(), "Request token value is not set");
        $this->assertNotEmpty($requestToken->getRequestTokenSecret(), "Request token secret is not set");
    }

    public function testGetRequestTokenInvalidConsumerKey()
    {
        try {
            $oAuthClient = $this->_getOauthClient('invalid_key', self::$_consumerSecret);
            $oAuthClient->requestRequestToken();
        } catch (Exception $exception) {
            $this->assertContains('HTTP/1.1 401 Authorization Required', $exception->getMessage());
        }
    }

    public function testGetRequestTokenInvalidConsumerSecret()
    {
        try {
            $oAuthClient = $this->_getOauthClient(self::$_consumerKey, 'invalid_secret');
            $oAuthClient->requestRequestToken();
        } catch (Exception $exception) {
            $this->assertContains('HTTP/1.1 401 Authorization Required', $exception->getMessage());
        }
    }


    /**
     * @magentoApiDataFixture consumerFixture
     */
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
    }


    /**
     * @magentoApiDataFixture consumerFixture
     */
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
        $this->assertEquals('testProduct2',
                            $responseArray[1]->name,
                            'Invocation to /rest/V1/testmodule1 expected to return testProduct2 but returned '
                            . $responseArray[1]->name);
    }

    /**
     * @magentoApiDataFixture consumerFixture
     */
    public function testGetAccessTokenInvalidVerifier()
    {
        try {
            $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
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
     * @magentoApiDataFixture consumerFixture
     */
    public function testAccessApiInvalidAccessToken()
    {
        try {
            $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
            $requestToken = $oAuthClient->requestRequestToken();
            $accessToken = $oAuthClient->requestAccessToken(
                $requestToken->getRequestToken(),
                self::$_verifier,
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

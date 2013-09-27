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
namespace Magento\Webapi\Authentication;

class RestTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /** @var \Magento\TestFramework\Authentication\Rest\OauthClient[] */
    protected $_oAuthClients = array();

    /** @var \Magento\Oauth\Model\Consumer */
    protected static $_consumer;

    /** @var \Magento\Oauth\Model\Token */
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
     * Create a consumer
     */
    public static function consumerFixture($date = null)
    {
        $consumerCredentials = \Magento\TestFramework\Authentication\OauthHelper::getConsumerCredentials($date);
        self::$_consumerKey = $consumerCredentials['key'];
        self::$_consumerSecret = $consumerCredentials['secret'];
        self::$_verifier = $consumerCredentials['verifier'];
        self::$_consumer = $consumerCredentials['consumer'];
        self::$_token = $consumerCredentials['token'];
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
        /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oAuthClient->requestRequestToken();

        $this->assertNotEmpty($requestToken->getRequestToken(), "Request token value is not set");
        $this->assertNotEmpty($requestToken->getRequestTokenSecret(), "Request token secret is not set");

        $this->assertEquals(\Magento\Oauth\Model\Token::LENGTH_TOKEN,
            strlen($requestToken->getRequestToken()),
            "Request token value length should be " . \Magento\Oauth\Model\Token::LENGTH_TOKEN);
        $this->assertEquals(\Magento\Oauth\Model\Token::LENGTH_SECRET,
            strlen($requestToken->getRequestTokenSecret()),
            "Request token secret length should be " . \Magento\Oauth\Model\Token::LENGTH_SECRET);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HTTP/1.1 401 Authorization Required
     */
    public function testGetRequestTokenExpiredConsumer()
    {
        $this::consumerFixture('2012-01-01 00:00:00');
        /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
        $oAuthClient = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $oAuthClient->requestRequestToken();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HTTP/1.1 401 Authorization Required
     */
    public function testGetRequestTokenInvalidConsumerKey()
    {
        $oAuthClient = $this->_getOauthClient('invalid_key', self::$_consumerSecret);
        $oAuthClient->requestRequestToken();
    }

    /**
     * @expectedException \Exception
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

        $this->assertEquals(\Magento\Oauth\Model\Token::LENGTH_TOKEN,
            strlen($accessToken->getAccessToken()),
            "Access token value length should be " . \Magento\Oauth\Model\Token::LENGTH_TOKEN);
        $this->assertEquals(\Magento\Oauth\Model\Token::LENGTH_SECRET,
            strlen($accessToken->getAccessTokenSecret()),
            "Access token secret length should be " . \Magento\Oauth\Model\Token::LENGTH_SECRET);
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
     * @expectedException \Exception
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

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HTTP/1.1 401
     */
    public function testGetAccessTokenConsumerMismatch()
    {
        $oAuthClientA = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);
        $requestTokenA = $oAuthClientA->requestRequestToken();
        $oauthVerifierA = self::$_verifier;

        self::consumerFixture();
        $oAuthClientB = $this->_getOauthClient(self::$_consumerKey, self::$_consumerSecret);

        $oAuthClientB->requestAccessToken(
            $requestTokenA->getRequestToken(),
            $oauthVerifierA,
            $requestTokenA->getRequestTokenSecret()
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HTTP/1.1 401
     */
    public function testAccessApiInvalidAccessToken()
    {
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

    protected function _getOauthClient($consumerKey, $consumerSecret)
    {
        if (!isset($this->_oAuthClients[$consumerKey])) {
            $credentials = new OAuth\Common\Consumer\Credentials($consumerKey, $consumerSecret, TESTS_BASE_URL);
            $this->_oAuthClients[$consumerKey] =
                new \Magento\TestFramework\Authentication\Rest\OauthClient($credentials);
        }
        return $this->_oAuthClients[$consumerKey];
    }

}

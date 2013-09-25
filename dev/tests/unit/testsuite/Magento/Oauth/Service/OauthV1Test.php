<?php
/**
 * Magento_Oauth_Service_OauthV1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Oauth_Service_OauthV1Test extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Oauth_Model_Consumer_Factory*/
    private $_consumerFactory;

    /** @var Magento_Oauth_Model_Nonce_Factory */
    private $_nonceFactory;

    /** @var Magento_Oauth_Model_Token_Factory */
    private $_tokenFactory;

    /** @var Magento_Oauth_Model_Consumer */
    private $_consumerMock;

    /** @var Magento_Oauth_Model_Token */
    private $_tokenMock;

    /** @var Magento_Oauth_Helper_Service */
    private $_helperMock;

    /** @var Magento_Core_Model_StoreManagerInterface */
    private $_storeManagerMock;

    /** @var Magento_HTTP_ZendClient */
    private $_httpClientMock;

    /** @var Magento_Oauth_Service_OauthV1 */
    private $_service;

    /** @var  Zend_Oauth_Http_Utility */
    private $_httpUtilityMock;

    const OAUTH_TOKEN = '11111111111111111111111111111111';
    const OAUTH_SECRET = '22222222222222222222222222222222';
    const CONSUMER_ID = 1;
    const TOKEN_VERIFIER = '00000000000000000000000000000000';

    public function setUp()
    {
        $this->_consumerFactory = $this->getMockBuilder('Magento_Oauth_Model_Consumer_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_consumerMock = $this->getMockBuilder('Magento_Oauth_Model_Consumer')
            ->disableOriginalConstructor()
            // Mocking magic getCreatedAt()
            ->setMethods([
                'getCreatedAt',
                'loadByKey',
                'load',
                'getId',
                'getSecret',
                'getCallbackUrl',
                'save',
                'getData'
            ])->getMock();
        $this->_consumerFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_consumerMock));

        $this->_nonceFactory = $this->getMockBuilder('Magento_Oauth_Model_Nonce_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_tokenFactory = $this->getMockBuilder('Magento_Oauth_Model_Token_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_tokenMock = $this->getMockBuilder('Magento_Oauth_Model_Token')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getId',
                    'load',
                    'getType',
                    'createRequestToken',
                    'getToken',
                    'getSecret',
                    'createVerifierToken',
                    'getVerifier',
                    'getConsumerId',
                    'convertToAccess',
                    'getRevoked',
                ]
            )->getMock();

        $this->_tokenFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));

        $this->_helperMock = $this->getMockBuilder('Magento_Oauth_Helper_Service')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeManagerMock = $this->getMockBuilder('Magento_Core_Model_StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_httpClientMock = $this->getMockBuilder('Magento_HTTP_ZendClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_httpUtilityMock = $this->getMock('Zend_Oauth_Http_Utility');

        $this->_service = new Magento_Oauth_Service_OauthV1(
            $this->_consumerFactory,
            $this->_nonceFactory,
            $this->_tokenFactory,
            $this->_helperMock,
            $this->_storeManagerMock,
            $this->_httpClientMock,
            $this->_httpUtilityMock
        );
    }

    public function tearDown()
    {
        unset($this->_consumerFactory);
        unset($this->_nonceFactory);
        unset($this->_tokenFactory);
        unset($this->_helperMock);
        unset($this->_storeManagerMock);
        unset($this->_httpClientMock);
        unset($this->_service);
    }

    public function testCreateConsumer()
    {
        $key = $this->_generateRandomString(Magento_Oauth_Model_Consumer::KEY_LENGTH);
        $secret = $this->_generateRandomString(Magento_Oauth_Model_Consumer::SECRET_LENGTH);

        $consumerData = array(
            'name' => 'Add-On Name', 'key' => $key, 'secret' => $secret, 'http_post_url' => 'http://www.magento.com');

        $this->_consumerMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->_consumerMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($consumerData));

        $responseData = $this->_service->createConsumer($consumerData);

        $this->assertEquals($key, $responseData['key'], 'Checking Oauth Consumer Key');
        $this->assertEquals($secret, $responseData['secret'], 'Checking Oauth Consumer Secret');
    }

    public function testPostToConsumer()
    {
        $consumerId = 1;
        $requestData = array('consumer_id' => $consumerId);

        $key = $this->_generateRandomString(Magento_Oauth_Model_Consumer::KEY_LENGTH);
        $secret = $this->_generateRandomString(Magento_Oauth_Model_Consumer::SECRET_LENGTH);
        $oauthVerifier = $this->_generateRandomString(Magento_Oauth_Model_Token::LENGTH_VERIFIER);

        $consumerData = array(
            'entity_id' => $consumerId,
            'key' => $key,
            'secret' => $secret,
            'http_post_url' => 'http://www.magento.com'
        );

        $this->_consumerMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($consumerId))
            ->will($this->returnSelf());
        $this->_consumerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($consumerId));
        $this->_consumerMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($consumerData));
        $storeMock = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeManagerMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://www.my-store.com/'));
        $this->_httpClientMock->expects($this->once())
            ->method('setUri')
            ->with('http://www.magento.com')
            ->will($this->returnSelf());
        $this->_httpClientMock->expects($this->once())
            ->method('setParameterPost')
            ->will($this->returnSelf());
        $this->_tokenMock->expects($this->once())
            ->method('createVerifierToken')
            ->with($consumerId)
            ->will($this->returnSelf());
        $this->_tokenMock->expects($this->once())
            ->method('getVerifier')
            ->will($this->returnValue($oauthVerifier));

        $responseData = $this->_service->postToConsumer($requestData);

        $this->assertEquals($oauthVerifier, $responseData['oauth_verifier']);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_VERSION_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 1
     */
    public function testGetRequestTokenVersionRejected()
    {
        $this->_service->getRequestToken(['oauth_version' => '2.0']);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 8
     */
    public function testGetRequestTokenConsumerKeyRejected()
    {
        $this->_service->getRequestToken(['oauth_version' => '1.0', 'oauth_consumer_key' => 'wrong_key_length']);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 8
     */
    public function testGetRequestTokenConsumerKeyNotFound()
    {
        $this->_consumerMock
            ->expects($this->once())
            ->method('loadByKey')
            ->will($this->returnValue(new Magento_Object()));

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH)
        ]);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_INVALID
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 17
     */
    public function testGetRequestTokenOutdatedConsumerKey()
    {
        $this->_setupConsumer();
        $this->_helperMock->expects($this->any())->method('getConsumerExpirationPeriod')->will($this->returnValue(0));

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH)
        ]);
    }

    protected function _setupConsumer($isLoadable = true)
    {
        $this->_consumerMock
            ->expects($this->any())
            ->method('loadByKey')
            ->will($this->returnSelf());

        $this->_consumerMock
            ->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(date('c', strtotime('-1 day'))));

        if ($isLoadable) {
            $this->_consumerMock->expects($this->any())->method('load')->will($this->returnSelf());
        } else {
            $this->_consumerMock->expects($this->any())->method('load')->will($this->returnValue(new Magento_Object()));
        }

        $this->_consumerMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->_consumerMock->expects($this->any())->method('getSecret')->will($this->returnValue('consumer_secret'));
        $this->_consumerMock->expects($this->any())->method('getCallbackUrl')->will($this->returnValue('callback_url'));
    }

    protected function _makeValidExpirationPeriod()
    {
        $this->_helperMock
            ->expects($this->any())
            ->method('getConsumerExpirationPeriod')
            ->will($this->returnValue(2 * 24 * 60 * 60)); // 2 days
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TIMESTAMP_REFUSED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 4
     * @dataProvider dataProviderForGetRequestTokenNonceTimestampRefusedTest
     */
    public function testGetRequestTokenOauthTimestampRefused($timestamp)
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => $timestamp
        ]);
    }

    public function dataProviderForGetRequestTokenNonceTimestampRefusedTest()
    {
        return [[0], [time() + Magento_Oauth_Service_OauthV1::TIME_DEVIATION * 2]];
    }

    protected function _setupNonce($isUsed = false, $timestamp = 0)
    {
        $nonceMock = $this->getMockBuilder('Magento_Oauth_Model_Nonce')
            ->disableOriginalConstructor()
            ->setMethods([
                'getConsumerId',
                'loadByCompositeKey',
                'getTimestamp',
                'setNonce',
                'setConsumerId',
                'setTimestamp',
                'save'
            ])->getMock();

        $nonceMock->expects($this->any())->method('getConsumerId')->will($this->returnValue((int)$isUsed));
        $nonceMock->expects($this->any())->method('loadByCompositeKey')->will($this->returnSelf());
        $nonceMock->expects($this->any())->method('getTimestamp')->will($this->returnValue($timestamp));
        $nonceMock->expects($this->any())->method('setNonce')->will($this->returnSelf());
        $nonceMock->expects($this->any())->method('setConsumerId')->will($this->returnSelf());
        $nonceMock->expects($this->any())->method('setTimestamp')->will($this->returnSelf());
        $nonceMock->expects($this->any())->method('save')->will($this->returnSelf());
        $this->_nonceFactory->expects($this->any())->method('create')->will($this->returnValue($nonceMock));
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_NONCE_USED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 5
     */
    public function testGetRequestTokenNonceAlreadyUsed()
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce(true);

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time()
        ]);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_PARAMETER_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 3
     */
    public function testGetRequestTokenNoConsumer()
    {
        $this->_setupConsumer(false);
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce();

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time()
        ]);
    }


    /**
     * Magento_Oauth_Helper_Service::ERR_NONCE_USED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 5
     */
    public function testGetRequestTokenNonceTimestampAlreadyUsed()
    {
        $timestamp = time();
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce(false, $timestamp);

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => $timestamp
        ]);
    }

    protected function _setupToken(
        $doesExist = true,
        $type = Magento_Oauth_Model_Token::TYPE_VERIFIER,
        $consumerId = self::CONSUMER_ID,
        $verifier = self::TOKEN_VERIFIER,
        $isRevoked = false
    ) {
        $this->_tokenMock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($doesExist ? self::CONSUMER_ID : null));

        $this->_tokenMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->_tokenMock->expects($this->any())->method('getType')->will($this->returnValue($type));
        $this->_tokenMock->expects($this->any())->method('createRequestToken')->will($this->returnSelf());
        $this->_tokenMock->expects($this->any())->method('getToken')->will($this->returnValue(self::OAUTH_TOKEN));
        $this->_tokenMock->expects($this->any())->method('getSecret')->will($this->returnValue(self::OAUTH_SECRET));
        $this->_tokenMock->expects($this->any())->method('getConsumerId')->will($this->returnValue($consumerId));
        $this->_tokenMock->expects($this->any())->method('getVerifier')->will($this->returnValue($verifier));
        $this->_tokenMock->expects($this->any())->method('convertToAccess')->will($this->returnSelf());
        $this->_tokenMock->expects($this->any())->method('getRevoked')->will($this->returnValue($isRevoked));
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testGetRequestTokenTokenRejected()
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce();
        $this->_setupToken(false);

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time()
        ]);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testGetRequestTokenTokenRejectedByType()
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_REQUEST); // wrong type

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time()
        ]);
    }


    /**
     * Magento_Oauth_Helper_Service::ERR_SIGNATURE_METHOD_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 6
     */
    public function testGetRequestTokenSignatureMethodRejected()
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce();
        $this->_setupToken();

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time(),
            'oauth_signature_method' => 'wrong_method',
            'http_method' => '',
            'request_url' => '',
        ]);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_SIGNATURE_INVALID
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 7
     */
    public function testGetRequestTokenInvalidSignature()
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce();
        $this->_setupToken();

        $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time(),
            'oauth_signature_method' => Magento_Oauth_Helper_Service::SIGNATURE_SHA1,
            'http_method' => '',
            'request_url' => 'http://magento.ll',
            'oauth_signature' => 'invalid_signature'
        ]);
    }

    public function testGetRequestToken()
    {
        $this->_setupConsumer();
        $this->_makeValidExpirationPeriod();
        $this->_setupNonce();
        $this->_setupToken();

        $signature = 'valid_signature';
        $this->_httpUtilityMock->expects($this->any())->method('sign')->will($this->returnValue($signature));

        $requestToken = $this->_service->getRequestToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_nonce' => '',
            'oauth_timestamp' => time(),
            'oauth_signature_method' => Magento_Oauth_Helper_Service::SIGNATURE_SHA1,
            'http_method' => '',
            'request_url' => 'http://magento.ll',
            'oauth_signature' => $signature
        ]);

        $this->assertEquals(
            ['oauth_token' => self::OAUTH_TOKEN, 'oauth_token_secret' => self::OAUTH_SECRET],
            $requestToken
        );
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_VERSION_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 1
     */
    public function testGetAccessTokenVersionRejected()
    {
        $this->_service->getAccessToken($this->_getAccessTokenRequiredParams(['oauth_version' => '0.0']));
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_PARAMETER_ABSENT
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 2
     */
    public function testGetAccessTokenParameterAbsent()
    {
        $this->_service->getAccessToken([
            'oauth_version' => '1.0',
            'oauth_consumer_key' => '',
            'oauth_signature' => '',
            'oauth_signature_method' => '',
            'oauth_nonce' => '',
            'oauth_timestamp' => '',
            'oauth_token' => '',
            // oauth_verifier missing
        ]);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testGetAccessTokenTokenRejected()
    {
        $this->_service->getAccessToken($this->_getAccessTokenRequiredParams(['oauth_token' => 'invalid_token']));
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_SIGNATURE_METHOD_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 6
     */
    public function testGetAccessTokenSignatureMethodRejected()
    {
        $this->_service->getAccessToken(
            $this->_getAccessTokenRequiredParams(['oauth_signature_method' => 'invalid_method'])
        );
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_USED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 9
     */
    public function testGetAccessTokenTokenUsed()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_VERIFIER); // Wrong type

        $this->_service->getAccessToken($this->_getAccessTokenRequiredParams());
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testGetAccessTokenConsumerIdDoesntMatch()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_REQUEST, null); // $token->getConsumerId() === null

        $this->_service->getAccessToken($this->_getAccessTokenRequiredParams());
    }

    /**
     * Magento_Oauth_Helper_Data::ERR_VERIFIER_INVALID
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 13
     * @dataProvider dataProviderForGetAccessTokenVerifierInvalidTest
     */
    public function testGetAccessTokenVerifierInvalid($verifier, $verifierFromToken)
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_REQUEST, self::CONSUMER_ID, $verifierFromToken);

        $this->_service->getAccessToken($this->_getAccessTokenRequiredParams(['oauth_verifier' => $verifier]));
    }

    public function dataProviderForGetAccessTokenVerifierInvalidTest()
    {
        return [
            [3, 3], // Verifier is not a string
            ['wrong_length', 'wrong_length'],
            ['verifier', 'doesnt match']
        ];
    }

    public function testGetAccessToken()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_REQUEST);

        $token = $this->_service->getAccessToken($this->_getAccessTokenRequiredParams());
        $this->assertEquals(['oauth_token' => self::OAUTH_TOKEN, 'oauth_token_secret' => self::OAUTH_SECRET], $token);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testValidateAccessTokenRequestTokenRejected()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_ACCESS, null); // $token->getConsumerId() === null

        $this->_service->validateAccessTokenRequest($this->_getAccessTokenRequiredParams());
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testValidateAccessTokenRequestTokenRejectedByType()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_REQUEST);

        $this->_service->validateAccessTokenRequest($this->_getAccessTokenRequiredParams());
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REVOKED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 11
     */
    public function testValidateAccessTokenRequestTokenRevoked()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_ACCESS, self::CONSUMER_ID, self::TOKEN_VERIFIER, true);

        $this->_service->validateAccessTokenRequest($this->_getAccessTokenRequiredParams());
    }

    public function testValidateAccessTokenRequest()
    {
        $this->_setupConsumer();
        $this->_setupNonce();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_ACCESS);

        $this->assertTrue($this->_service->validateAccessTokenRequest($this->_getAccessTokenRequiredParams()));
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 12
     */
    public function testValidateAccessTokenRejectedByType()
    {
        $this->_setupConsumer();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_REQUEST);

        $this->_service->validateAccessToken(self::OAUTH_TOKEN);
    }

    /**
     * Magento_Oauth_Helper_Service::ERR_TOKEN_REVOKED
     * @expectedException Magento_Oauth_Exception
     * @expectedExceptionCode 11
     */
    public function testValidateAccessTokenRevoked()
    {
        $this->_setupConsumer();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_ACCESS, self::CONSUMER_ID, self::TOKEN_VERIFIER, true);

        $this->_service->validateAccessToken(self::OAUTH_TOKEN);
    }

    public function testValidateAccessToken()
    {
        $this->_setupConsumer();
        $this->_setupToken(true, Magento_Oauth_Model_Token::TYPE_ACCESS);

        $this->assertTrue($this->_service->validateAccessToken(self::OAUTH_TOKEN));
    }

    protected function _getAccessTokenRequiredParams(array $amendments = [])
    {
        $requiredParams = [
            'oauth_consumer_key' => str_repeat('0', Magento_Oauth_Model_Consumer::KEY_LENGTH),
            'oauth_signature' => '',
            'oauth_signature_method' => (string)Magento_Oauth_Helper_Service::SIGNATURE_SHA1,
            'oauth_nonce' => '',
            'oauth_timestamp' => (string)time(),
            'oauth_token' => str_repeat('0', Magento_Oauth_Model_Token::LENGTH_TOKEN),
            'oauth_verifier' => self::TOKEN_VERIFIER,
            'request_url' => '',
            'http_method' => '',
        ];

        return array_merge($requiredParams, $amendments);
    }

    private function _generateRandomString($length)
    {
        return substr(str_shuffle(
                str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, $length);
    }
}

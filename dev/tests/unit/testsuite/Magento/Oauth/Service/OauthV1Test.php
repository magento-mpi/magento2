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

    /** @var Magento_Oauth_Helper_Data */
    private $_helperMock;

    /** @var Magento_Core_Model_Store */
    private $_storeMock;

    /** @var Magento_HTTP_ZendClient */
    private $_httpClientMock;

    /** @var Magento_Oauth_Service_OauthV1 */
    private $_service;

    public function setUp()
    {
        $this->_consumerFactory = $this->getMockBuilder('Magento_Oauth_Model_Consumer_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_consumerMock = $this->getMockBuilder('Magento_Oauth_Model_Consumer')
            ->disableOriginalConstructor()
            ->getMock();
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
            ->getMock();
        $this->_tokenFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));

        $this->_helperMock = $this->getMockBuilder('Magento_Oauth_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        $storeManagerMock = $this->getMockBuilder('Magento_Core_Model_StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_storeMock = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManagerMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));

        $this->_httpClientMock = $this->getMockBuilder('Magento_HTTP_ZendClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_service = new Magento_Oauth_Service_OauthV1(
            $this->_consumerFactory,
            $this->_nonceFactory,
            $this->_tokenFactory,
            $this->_helperMock,
            $storeManagerMock,
            $this->_httpClientMock
        );
    }

    public function tearDown()
    {
        unset($this->_consumerFactory);
        unset($this->_nonceFactory);
        unset($this->_tokenFactory);
        unset($this->_helperMock);
        unset($this->_storeMock);
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
        $this->_storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://www.my-store.com/'));
        $this->_httpClientMock->expects($this->once())
            ->method('setUri')
            ->with($this->equalTo('http://www.magento.com'))
            ->will($this->returnSelf());
        $this->_httpClientMock->expects($this->once())
            ->method('setParameterPost')
            ->will($this->returnSelf());
        $this->_tokenMock->expects($this->once())
            ->method('createVerifierToken')
            ->with($this->equalTo($consumerId))
            ->will($this->returnSelf());
        $this->_tokenMock->expects($this->once())
            ->method('getVerifier')
            ->will($this->returnValue($oauthVerifier));

        $responseData = $this->_service->postToConsumer($requestData);

        $this->assertEquals($oauthVerifier, $responseData['oauth_verifier']);
    }

    private function _generateRandomString($length)
    {
        return substr(str_shuffle(
                str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, $length);
    }
}

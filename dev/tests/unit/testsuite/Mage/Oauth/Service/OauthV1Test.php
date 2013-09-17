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
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_consumerFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_nonceFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_tokenFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_consumerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_tokenMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_helperFactoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_storeMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_translator;

    /** @var PHPUnit_Framework_MockObject_MockObject */
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

        $this->_helperFactoryMock = $this->getMockBuilder('Magento_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Magento_Oauth_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_helperFactoryMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Magento_Oauth_Helper_Data'))
            ->will($this->returnValue($this->_helperMock));

        $this->_storeMock = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_translator = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_translator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(
                    function ($arr) {
                        return $arr[0];
                    }
                ));

        $this->_httpClientMock = $this->getMockBuilder('Zend_Http_Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_service = new Magento_Oauth_Service_OauthV1(
            $this->_consumerFactory,
            $this->_nonceFactory,
            $this->_tokenFactory,
            $this->_helperFactoryMock,
            $this->_storeMock,
            $this->_translator,
            $this->_httpClientMock
        );
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
        $key = $this->_generateRandomString(Magento_Oauth_Model_Consumer::KEY_LENGTH);
        $secret = $this->_generateRandomString(Magento_Oauth_Model_Consumer::SECRET_LENGTH);

        $consumerData =
            array('entity_id' => 1, 'key' => $key, 'secret' => $secret, 'http_post_url' => 'http://www.magento.com');

        $oauthVerifier = $this->_generateRandomString(Magento_Oauth_Model_Token::LENGTH_VERIFIER);

        $this->_storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://www.my-store.com/'));
        $this->_httpClientMock->expects($this->once())
            ->method('setUri')
            ->with($this->equalTo('http://www.magento.com'))
            ->will($this->returnValue($this->_httpClientMock));
        $this->_httpClientMock->expects($this->once())
            ->method('setParameterPost')
            ->will($this->returnValue($this->_httpClientMock));
        $this->_tokenMock->expects($this->once())
            ->method('createVerifierToken')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->_tokenMock));
        $this->_tokenMock->expects($this->once())
            ->method('getVerifier')
            ->will($this->returnValue($oauthVerifier));

        $responseData = $this->_service->postToConsumer($consumerData);

        $this->assertEquals($oauthVerifier, $responseData['oauth_verifier']);
    }

    private function _generateRandomString($length)
    {
        return substr(str_shuffle(
                str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, $length);
    }
}
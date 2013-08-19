<?php
/**
 * Mage_Oauth_Service_OauthV1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Oauth_Service_OauthV1Test extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_consumerFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_nonceFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_consumerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_helperFactoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_translator;

    /** @var Mage_Oauth_Service_OauthV1 */
    private $_service;

    public function setUp()
    {
        $this->_consumerFactory = $this->getMockBuilder('Mage_Oauth_Model_Consumer_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_consumerMock = $this->getMockBuilder('Mage_Oauth_Model_Consumer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_consumerFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_consumerMock));

        $this->_nonceFactory = $this->getMockBuilder('Mage_Oauth_Model_Nonce_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_helperFactoryMock = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Oauth_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_helperFactoryMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Oauth_Helper_Data'))
            ->will($this->returnValue($this->_helperMock));

        $this->_translator = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_translator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(
                    function ($arr) {
                        return $arr[0];
                    }
                ));

        $this->_service = new Mage_Oauth_Service_OauthV1(
            $this->_consumerFactory,
            $this->_nonceFactory,
            $this->_helperFactoryMock,
            $this->_translator);
    }

    public function testCreateConsumer()
    {
        $key = $this->_generateRandomString(Mage_Oauth_Model_Consumer::KEY_LENGTH);
        $secret = $this->_generateRandomString(Mage_Oauth_Model_Consumer::SECRET_LENGTH);

        $consumerData = array(
            'name' => 'Add-On Name', 'key' => $key, 'secret' => $secret, 'http_post_url' => 'http://www.magento.com');

        $this->_consumerMock->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue($key));
        $this->_consumerMock->expects($this->once())
            ->method('getSecret')
            ->will($this->returnValue($secret));
        $this->_consumerMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $responseData = $this->_service->createConsumer($consumerData);

        $this->assertEquals($key, $responseData['oauth_consumer_key'], 'Checking Oauth Consumer Key');
        $this->assertEquals($secret, $responseData['oauth_consumer_secret'], 'Checking Oauth Consumer Secret');
    }

    private function _generateRandomString($length)
    {
        return substr(str_shuffle(
                str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, $length);
    }
}
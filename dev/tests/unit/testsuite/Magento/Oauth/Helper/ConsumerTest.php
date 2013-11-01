<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Oauth\Helper;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManagerMock;

    /** @var \Magento\Oauth\Model\Consumer\Factory */
    protected $_consumerFactory;

    /** @var \Magento\Oauth\Model\Consumer */
    protected $_consumerMock;

    /** @var \Magento\HTTP\ZendClient */
    protected $_httpClientMock;

    /** @var \Magento\Oauth\Model\Token\Factory */
    protected $_tokenFactory;

    /** @var \Magento\Oauth\Model\Token */
    protected $_tokenMock;

    /** @var \Magento\Core\Model\Store */
    protected $_storeMock;

    /** @var \Magento\Oauth\Helper\Data */
    protected $_dataHelper;

    /** @var \Magento\Oauth\Helper\Consumer */
    protected $_consumerHelper;

    protected function setUp()
    {
        $this->_consumerFactory = $this->getMockBuilder('Magento\Oauth\Model\Consumer\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_consumerMock = $this->getMockBuilder('Magento\Oauth\Model\Consumer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_consumerFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_consumerMock));

        $this->_tokenFactory = $this->getMockBuilder('Magento\Oauth\Model\Token\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenMock = $this->getMockBuilder('Magento\Oauth\Model\Token')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));

        $this->_storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_storeMock = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));

        $this->_dataHelper = $this->getMockBuilder('Magento\Oauth\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_httpClientMock = $this->getMockBuilder('Magento\HTTP\ZendClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_consumerHelper = new \Magento\Oauth\Helper\Consumer(
            $this->_storeManagerMock,
            $this->_consumerFactory,
            $this->_tokenFactory,
            $this->_dataHelper,
            $this->_httpClientMock
        );
    }

    protected function tearDown()
    {
        unset($this->_storeManagerMock);
        unset($this->_consumerFactory);
        unset($this->_tokenFactory);
        unset($this->_dataHelper);
        unset($this->_httpClientMock);
        unset($this->_consumerHelper);
    }

    public function testCreateConsumer()
    {
        $key = $this->_generateRandomString(\Magento\Oauth\Helper\Oauth::KEY_LENGTH);
        $secret = $this->_generateRandomString(\Magento\Oauth\Helper\Oauth::SECRET_LENGTH);

        $consumerData = array(
            'name' => 'Integration Name',
            'key' => $key,
            'secret' => $secret,
            'http_post_url' => 'http://www.magento.com'
        );

        $this->_consumerMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->_consumerMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($consumerData));

        $responseData = $this->_consumerHelper->createConsumer($consumerData);

        $this->assertEquals($key, $responseData['key'], 'Checking Oauth Consumer Key');
        $this->assertEquals($secret, $responseData['secret'], 'Checking Oauth Consumer Secret');
    }

    public function testPostToConsumer()
    {
        $consumerId = 1;

        $key = $this->_generateRandomString(\Magento\Oauth\Helper\Oauth::KEY_LENGTH);
        $secret = $this->_generateRandomString(\Magento\Oauth\Helper\Oauth::SECRET_LENGTH);
        $oauthVerifier = $this->_generateRandomString(\Magento\Oauth\Helper\Oauth::LENGTH_VERIFIER);

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
        $this->_tokenMock->expects($this->any())
            ->method('getVerifier')
            ->will($this->returnValue($oauthVerifier));
        $this->_dataHelper->expects($this->once())
            ->method('getConsumerPostMaxRedirects')
            ->will($this->returnValue(5));
        $this->_dataHelper->expects($this->once())
            ->method('getConsumerPostTimeout')
            ->will($this->returnValue(120));

        $verifier = $this->_consumerHelper->postToConsumer($consumerId);

        $this->assertEquals($oauthVerifier, $verifier, 'Checking Oauth Verifier');
    }

    private function _generateRandomString($length)
    {
        return substr(
            str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, $length
        );
    }
}

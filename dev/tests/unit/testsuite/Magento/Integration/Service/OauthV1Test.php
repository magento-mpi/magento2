<?php
/**
 * Test for \Magento\Integration\Service\OauthV1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service;

use Magento\Integration\Model\Integration;

class OauthV1Test extends \PHPUnit_Framework_TestCase
{
    const VALUE_CONSUMER_ID = 1;

    /** @var \Magento\Integration\Model\Oauth\Consumer\Factory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_consumerFactory;

    /** @var \Magento\Integration\Model\Oauth\Token\Provider|\PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenProviderMock;

    /** @var \Magento\Integration\Model\Oauth\Consumer|\PHPUnit_Framework_MockObject_MockObject */
    private $_consumerMock;

    /** @var \Magento\Integration\Model\Integration|\PHPUnit_Framework_MockObject_MockObject */
    private $_emptyConsumerMock;

    /**
     * @var \Magento\Integration\Model\Oauth\Token|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_tokenMock;

    /** @var \Magento\Integration\Service\OauthV1 */
    private $_service;

    /** @var array */
    private $_consumerData;

    /**
     * @var \Magento\Integration\Model\Oauth\Token\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_tokenFactoryMock;

    protected function setUp()
    {
        $this->_consumerFactory = $this->getMockBuilder('Magento\Integration\Model\Oauth\Consumer\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenProviderMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token\Provider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_tokenFactoryMock = $this->getMock('Magento\Integration\Model\Oauth\Token\Factory', [], [], '', false);
        $this->_consumerMock = $this->getMockBuilder('Magento\Integration\Model\Oauth\Consumer')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getData',
                    'getId',
                    'load',
                    'save',
                    'delete',
                    '__wakeup'
                ]
            )
            ->getMock();
        $this->_consumerData = array(
            'entity_id' => self::VALUE_CONSUMER_ID,
            'key' => 'jhgjhgjgjiyuiuyuyhhhjkjlklkj',
            'secret' => 'iuyytrfdsdfbnnhbmkkjlkjl',
            'created_at' => '',
            'updated_at' => '',
            'callback_url' => '',
            'rejected_callback_url' => ''
        );
        $this->_consumerFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_consumerMock));

        $this->_service = new \Magento\Integration\Service\OauthV1(
            $this->getMock('Magento\Core\Model\StoreManagerInterface', [], [], '', false),
            $this->_consumerFactory,
            $this->_tokenFactoryMock,
            $this->getMock('Magento\Integration\Helper\Oauth\Data', [], [], '', false),
            $this->getMock('Magento\HTTP\ZendClient', [], [], '', false),
            $this->getMock('Magento\Logger', [], [], '', false),
            $this->getMock('Magento\Oauth\Helper\Oauth', [], [], '', false),
            $this->_tokenProviderMock
        );
        $this->_emptyConsumerMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getData',
                    'load',
                    'getId',
                    'save',
                    'delete',
                    '__wakeup'
                ]
            )
            ->getMock();
        $this->_emptyConsumerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
    }

    public function testDelete()
    {
        $this->_consumerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_CONSUMER_ID));
        $this->_consumerMock->expects($this->once())
            ->method('load')
            ->with(self::VALUE_CONSUMER_ID)
            ->will($this->returnValue($this->_consumerMock));
        $this->_consumerMock->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($this->_consumerMock));
        $this->_consumerMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_consumerData));
        $consumerData = $this->_service->deleteConsumer(self::VALUE_CONSUMER_ID);
        $this->assertEquals($this->_consumerData['entity_id'], $consumerData['entity_id']);
    }

    /**
     * @expectedException \Magento\Integration\Exception
     * @expectedExceptionMessage Consumer with ID '1' does not exist.
     */
    public function testDeleteException()
    {
        $this->_consumerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->_consumerMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_consumerMock->expects($this->never())
            ->method('delete');
        $this->_service->deleteConsumer(self::VALUE_CONSUMER_ID);
    }

    public function testCreateAccessTokenWithoutClearExisting()
    {

        $this->_consumerMock->expects($this->any())
            ->method('load')
            ->with(self::VALUE_CONSUMER_ID)
            ->will($this->returnValue($this->_consumerMock));

        $this->_tokenProviderMock->expects($this->any())
            ->method('getTokenByConsumerId')
            ->will($this->returnValue($this->_tokenMock));

        $this->_tokenProviderMock->expects($this->any())
            ->method('createRequestToken')
            ->with($this->_consumerMock);

        $this->_tokenProviderMock->expects($this->any())
            ->method('getAccessToken')
            ->with($this->_consumerMock);

        $this->_tokenFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));

        $this->_tokenMock->expects($this->any())
            ->method('delete');

        $this->_tokenMock->expects($this->once())
            ->method('createVerifierToken')
            ->with(self::VALUE_CONSUMER_ID);

        $this->_tokenProviderMock->expects($this->once())
            ->method('createRequestToken')
            ->with($this->_consumerMock);

        $this->_tokenProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with($this->_consumerMock);

        $this->assertTrue($this->_service->createAccessToken(self::VALUE_CONSUMER_ID, true));
    }

    public function testCreateAccessTokenWithClearExisting()
    {
        $this->_consumerMock->expects($this->any())
            ->method('load')
            ->with(self::VALUE_CONSUMER_ID)
            ->will($this->returnValue($this->_consumerMock));

        $this->_tokenProviderMock->expects($this->any())
            ->method('getTokenByConsumerId')
            ->will($this->returnValue($this->_tokenMock));

        $this->_tokenMock->expects($this->never())
            ->method('delete');

        $this->assertFalse($this->_service->createAccessToken(self::VALUE_CONSUMER_ID, false));
    }

    public function testCreateAccessTokenWithoutExisting()
    {
        $this->_consumerMock->expects($this->any())
            ->method('load')
            ->with(0)
            ->will($this->returnValue($this->_consumerMock));

        $this->_tokenProviderMock->expects($this->any())
            ->method('getTokenByConsumerId')
            ->will($this->returnValue(false));

        $this->_tokenMock->expects($this->never())
            ->method('delete');

        $this->_tokenFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));

        $this->_tokenMock->expects($this->never())
            ->method('createVerifierToken');

        $this->_tokenProviderMock->expects($this->never())
            ->method('createRequestToken');

        $this->_tokenProviderMock->expects($this->never())
            ->method('getAccessToken');

        $this->assertFalse($this->_service->createAccessToken(0, false));
    }
}

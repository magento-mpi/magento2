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

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_consumerFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_consumerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_emptyConsumerMock;

    /** @var \Magento\Integration\Service\OauthV1 */
    private $_service;

    /** @var array */
    private $_consumerData;

    protected function setUp()
    {
        $this->_consumerFactory = $this->getMockBuilder('Magento\Integration\Model\Oauth\Consumer\Factory')
            ->disableOriginalConstructor()
            ->getMock();
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
            $this->_consumerFactory
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
}

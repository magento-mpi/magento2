<?php
/**
 * Test for \Magento\Integration\Service\IntegrationV1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service;

class IntegrationV1Test extends \PHPUnit_Framework_TestCase
{
    const VALUE_INTEGRATION_ID = 1;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_integrationFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_integrationMock;

    /** @var \Magento\Integration\Service\IntegrationV1 */
    private $_service;

    /** @var array */
    private $_integrationData;

    protected function setUp()
    {
        $this->_integrationFactory = $this->getMockBuilder('Magento\Integration\Model\Integration\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_integrationMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getData',
                    'getId',
                    'getName',
                    'getEmail',
                    'getAuthentication',
                    'getEndpoint',
                    'load',
                    'save',
                    '__wakeup'
                ]
            )
            ->getMock();
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationData = array(
            'integration_id' => self::VALUE_INTEGRATION_ID,
            'name' => 'Integration Name',
            'email' => 'test@maento.com',
            'authentication' => 1,
            'endpoint' => 'http://magento.ll/endpoint'
        );
        $this->_integrationMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_integrationData));
        $this->_integrationFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_integrationMock));
        $this->_integrationMock->expects($this->any())
            ->method('load')
            ->with(self::VALUE_INTEGRATION_ID)
            ->will($this->returnValue($this->_integrationMock));
        $this->_service = new \Magento\Integration\Service\IntegrationV1(
            $this->_integrationFactory
        );
    }

    public function testCreate()
    {
        $this->_integrationMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->_setValidIntegrationData();
        $resultData = $this->_service->create($this->_integrationData);
        $this->assertSame($this->_integrationData, $resultData);
    }

    /**
     * @expectedException \Magento\Integration\Exception
     * @expectedExceptionMessage Please enter data for all the required fields.
     */
    public function testCreateException()
    {
        $this->_integrationMock->expects($this->never())
            ->method('save')
            ->will($this->throwException(new \Exception()));
        $this->_service->create($this->_integrationData);
    }

    public function testUpdate()
    {
        $this->_setValidIntegrationData();
        $this->_integrationMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $integrationData = $this->_service->update($this->_integrationData);
        $this->assertEquals($this->_integrationData, $integrationData);
    }

    /**
     * @expectedException \Magento\Integration\Exception
     * @expectedExceptionMessage Please enter data for all the required fields.
     */
    public function testUpdateException()
    {
        $this->_integrationMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_integrationMock->expects($this->never())
            ->method('save')
            ->will($this->throwException(new \Exception()));
        $this->_service->update($this->_integrationData);
    }

    public function testGet()
    {
        $this->_integrationMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_integrationMock->expects($this->never())
            ->method('save');
        $integrationData = $this->_service->get(self::VALUE_INTEGRATION_ID);
        $this->assertEquals($this->_integrationData, $integrationData);
    }

    /**
     * @expectedException \Magento\Integration\Exception
     * @expectedExceptionMessage Unexpected
     */
    public function testGetException()
    {
        $this->_integrationFactory->expects($this->any())
            ->method('create')
            ->will($this->throwException(new \Exception()));
        $this->_integrationMock->expects($this->never())
            ->method('save');
        $integrationData = $this->_service->get(self::VALUE_INTEGRATION_ID);
        $this->assertEquals($this->_integrationData, $integrationData);
    }

    private function _setValidIntegrationData()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testName'));

        $this->_integrationMock->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('testEmail'));

        $this->_integrationMock->expects($this->any())
            ->method('getAuthentication')
            ->will($this->returnValue('1'));

        $this->_integrationMock->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue('http://magento.ll/endpoint'));
    }
}
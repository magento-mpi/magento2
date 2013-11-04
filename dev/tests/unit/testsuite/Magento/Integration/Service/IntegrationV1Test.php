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
    const VALUE_INTEGRATION_NAME = 'Integration Name';
    const VALUE_INTEGRATION_ANOTHER_NAME = 'Another Integration Name';
    const VALUE_INTEGRATION_EMAIL = 'test@magento.com';
    const VALUE_INTEGRATION_ENDPOINT = 'http://magento.ll/endpoint';

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_integrationFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_integrationMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_emptyIntegrationMock;

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
                    'loadByName',
                    'save',
                    '__wakeup'
                ]
            )
            ->getMock();
        $this->_integrationData = array(
            'integration_id' => self::VALUE_INTEGRATION_ID,
            'name' => self::VALUE_INTEGRATION_NAME,
            'email' => self::VALUE_INTEGRATION_EMAIL,
            'authentication' => 1,
            'endpoint' => self::VALUE_INTEGRATION_ENDPOINT
        );
        $this->_integrationFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_integrationMock));
        $this->_service = new \Magento\Integration\Service\IntegrationV1(
            $this->_integrationFactory
        );
        $this->_emptyIntegrationMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
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
                    'loadByName',
                    'save',
                    '__wakeup'
                ]
            )
            ->getMock();
        $this->_emptyIntegrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
    }

    public function testCreateSuccess()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_integrationData));
        $this->_integrationMock->expects($this->any())
            ->method('load')
            ->with(self::VALUE_INTEGRATION_NAME, 'name')
            ->will($this->returnValue($this->_emptyIntegrationMock));
        $this->_integrationMock->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $this->_setValidIntegrationData();
        $resultData = $this->_service->create($this->_integrationData);
        $this->assertSame($this->_integrationData, $resultData);
    }

    /**
     * @expectedException \Magento\Integration\Exception
     * @expectedExceptionMessage Integration with name 'Integration Name' exists.
     */
    public function testCreateIntegrationAlreadyExistsException()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_integrationData));
        $this->_integrationMock->expects($this->any())
            ->method('load')
            ->with(self::VALUE_INTEGRATION_NAME, 'name')
            ->will($this->returnValue($this->_integrationMock));
        $this->_integrationMock->expects($this->never())
            ->method('save')
            ->will($this->returnSelf());
        $this->_service->create($this->_integrationData);
    }

    public function testUpdateSuccess()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_integrationData));
        $this->_integrationMock->expects($this->at(0))
            ->method('load')
            ->with(self::VALUE_INTEGRATION_ID)
            ->will($this->returnValue($this->_integrationMock));
        $this->_integrationMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->_setValidIntegrationData();
        $integrationData = $this->_service->update($this->_integrationData);
        $this->assertEquals($this->_integrationData, $integrationData);
    }

    public function testUpdateSuccessNameChanged()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationMock->expects($this->any())
            ->method('load')
            ->will($this->onConsecutiveCalls($this->_integrationMock, $this->_emptyIntegrationMock));
        $this->_integrationMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->_setValidIntegrationData();
        $integrationData = array(
            'integration_id' => self::VALUE_INTEGRATION_ID,
            'name' => self::VALUE_INTEGRATION_ANOTHER_NAME,
            'email' => self::VALUE_INTEGRATION_EMAIL,
            'authentication' => 1,
            'endpoint' => self::VALUE_INTEGRATION_ENDPOINT
        );
        $this->_integrationMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($integrationData));

        $updatedData = $this->_service->update($integrationData);
        $this->assertEquals($integrationData, $updatedData);
    }

    /**
     * @expectedException \Magento\Integration\Exception
     * @expectedExceptionMessage Integration with name 'Another Integration Name' exists.
     */
    public function testUpdateException()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationMock->expects($this->any())
            ->method('load')
            ->will($this->onConsecutiveCalls($this->_integrationMock, $this->_getAnotherIntegrationMock()));
        $this->_integrationMock->expects($this->never())
            ->method('save')
            ->will($this->returnSelf());
        $this->_setValidIntegrationData();
        $integrationData = array(
            'integration_id' => self::VALUE_INTEGRATION_ID,
            'name' => self::VALUE_INTEGRATION_ANOTHER_NAME,
            'email' => self::VALUE_INTEGRATION_EMAIL,
            'authentication' => 1,
            'endpoint' => self::VALUE_INTEGRATION_ENDPOINT
        );
        $this->_service->update($integrationData);
    }

    public function testGet()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ID));
        $this->_integrationMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->_integrationData));
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
     * @expectedExceptionMessage Integration with ID '1' doesn't exist.
     */
    public function testGetException()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->_integrationMock->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $this->_integrationMock->expects($this->never())
            ->method('save');
        $this->_service->get(self::VALUE_INTEGRATION_ID);
    }

    /**
     * Set valid integration data
     */
    private function _setValidIntegrationData()
    {
        $this->_integrationMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::VALUE_INTEGRATION_NAME));
        $this->_integrationMock->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue(self::VALUE_INTEGRATION_EMAIL));
        $this->_integrationMock->expects($this->any())
            ->method('getAuthentication')
            ->will($this->returnValue('1'));
        $this->_integrationMock->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ENDPOINT));
    }

    /**
     * Create mock integration
     *
     * @param string $name
     * @param int $integrationId
     * @return mixed
     */
    private function _getAnotherIntegrationMock(
        $name = self::VALUE_INTEGRATION_NAME,
        $integrationId = self::VALUE_INTEGRATION_ID
    ) {
        $integrationMock = $this->getMockBuilder('Magento\Integration\Model\Integration')
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
                    'loadByName',
                    'save',
                    '__wakeup'
                ]
            )
            ->getMock();
        $integrationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($integrationId));
        $integrationMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $integrationMock->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue(self::VALUE_INTEGRATION_EMAIL));
        $integrationMock->expects($this->any())
            ->method('getAuthentication')
            ->will($this->returnValue('1'));
        $integrationMock->expects($this->any())
            ->method('getEndpoint')
            ->will($this->returnValue(self::VALUE_INTEGRATION_ENDPOINT));
        return $integrationMock;
    }
}
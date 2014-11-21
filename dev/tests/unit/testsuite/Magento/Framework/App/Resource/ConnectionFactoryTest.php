<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Resource;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Resource\ConnectionFactory
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Arguments|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localConfig;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('\Magento\Framework\ObjectManagerInterface', [], [], '', false);
        $this->localConfig = $this->getMock('\Magento\Framework\App\Arguments', [], [], '', false);

        $this->model = new \Magento\Framework\App\Resource\ConnectionFactory(
            $this->objectManager,
            $this->localConfig
        );
    }

    /**
     * @param array $config
     * @dataProvider dataProviderCreateNoActiveConfig
     */
    public function testCreateNoActiveConfig($config)
    {
        $this->localConfig->expects($this->once())
            ->method('getConnection')->with('connection_name')->will($this->returnValue($config));

        $this->assertNull($this->model->create('connection_name'));
    }

    /**
     * @return array
     */
    public function dataProviderCreateNoActiveConfig()
    {
        return [
            [null, null],
            [['value'], null],
            [['active' => 0], null],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Adapter is not set for connection "connection_name"
     */
    public function testCreateNoAdapter()
    {
        $config = [
            'active' => 1,
        ];

        $this->localConfig->expects($this->once())->method('getConnection')
            ->with('connection_name')->will($this->returnValue($config)
        );

        $this->model->create('connection_name');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Trying to create wrong connection adapter
     */
    public function testCreateNoWrongAdapter()
    {
        $config = [
            'active' => 1,
            'adapter' => 'StdClass',
        ];

        $this->localConfig->expects($this->once())->method('getConnection')
            ->with('connection_name')->will($this->returnValue($config)
        );

        $adapterMock = $this->getMock('StdClass');

        $this->objectManager->expects($this->once())->method('create')
            ->with('StdClass', $config)->will($this->returnValue($adapterMock)
        );

        $this->model->create('connection_name');
    }

    public function testCreate()
    {
        $config = [
            'active' => 1,
            'adapter' => 'Magento\Framework\App\Resource\ConnectionAdapterInterface',
        ];

        $this->localConfig->expects($this->once())->method('getConnection')
            ->with('connection_name')->will($this->returnValue($config));

        $adapterMock = $this->getMock('Magento\Framework\App\Resource\ConnectionAdapterInterface');

        $this->objectManager->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Resource\ConnectionAdapterInterface', $config)
            ->will($this->returnValue($adapterMock));

        $connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');

        $adapterMock->expects($this->once())->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $this->assertEquals($connectionMock, $this->model->create('connection_name'));
    }
}

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
    private $model;

    protected function setUp()
    {
        $this->model = new \Magento\Framework\App\Resource\ConnectionFactory(
            $this->getMockForAbstractClass('Magento\Framework\DB\LoggerInterface')
        );
    }

    /**
     * @param array $config
     * @dataProvider dataProviderCreateNoActiveConfig
     */
    public function testCreateNoActiveConfig($config)
    {
        $this->assertNull($this->model->create($config));
    }

    /**
     * @return array
     */
    public function dataProviderCreateNoActiveConfig()
    {
        return [
            [[]],
            [['value']],
            [['active' => 0]],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Adapter is not set for connection
     */
    public function testCreateNoAdapter()
    {
        $config = [
            'active' => 1,
        ];

        $this->model->create($config);
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

        $this->model->create($config);
    }

    public function testCreate()
    {
        $this->markTestSkipped('MAGETWO-30176: requires injection of object manager or service locator');

        $adapter = 'Magento\Framework\Model\Resource\Type\Db\Pdo\Mysql';
        $config = [
            'active' => 1,
            'adapter' => $adapter,
        ];

        $adapterMock = $this->getMock($adapter);

        $connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');

        $adapterMock->expects(
            $this->once()
        )->method(
            'getConnection'
        )->will(
            $this->returnValue($connectionMock)
        );

        $this->assertEquals($connectionMock, $this->model->create($config));
    }
}

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
     * @expectedExceptionMessage Trying to create wrong connection adapter
     */
    public function testCreateWrongAdapter()
    {
        $config = [
            'host' => 'localhost',
            'active' => 1,
            'adapter' => 'StdClass',
        ];

        $this->model->create($config);
    }
}

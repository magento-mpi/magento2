<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\Resource\Type\Db;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\Resource\Type\Db\ConnectionFactory
     */
    private $model;

    protected function setUp()
    {
        $this->model = new \Magento\Framework\Model\Resource\Type\Db\ConnectionFactory;
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

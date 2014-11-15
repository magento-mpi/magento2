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
     * @var ConnectionFactory
     */
    private $model;

    protected function setUp()
    {
        $this->model = new ConnectionFactory(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        );
    }

    public function testCreate()
    {
        $dbInstance = \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        $dbConfig = [
            'host' => $dbInstance->getHost(),
            'username' => $dbInstance->getUser(),
            'password' => $dbInstance->getPassword(),
            'dbname' => $dbInstance->getSchema(),
            'active' => true,
        ];
        $connection = $this->model->create($dbConfig);
        $this->assertInstanceOf('\Magento\Framework\DB\Adapter\AdapterInterface', $connection);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Resource;

class ConnectionFactoryCacheableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Resource\ConnectionFactoryCacheable
     */
    private $model;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = new \Magento\Framework\App\Resource\ConnectionFactoryCacheable(
            $objectManager->get('Magento\Framework\DB\LoggerInterface'),
            $objectManager->get('Magento\Framework\App\CacheInterface')
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
        $this->assertAttributeInstanceOf('\Magento\Framework\Cache\FrontendInterface', '_cacheAdapter', $connection);
    }
}

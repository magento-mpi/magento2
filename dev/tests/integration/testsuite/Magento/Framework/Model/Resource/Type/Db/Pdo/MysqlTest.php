<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Model\Resource\Type\Db\Pdo;

class MysqlTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConnection()
    {
        $db = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getBootstrap()->getApplication()->getDbInstance();
        $config = [
            'profiler' => [
                'class' => '\Magento\Framework\DB\Profiler',
                'enabled' => true,
            ],
            'type' => 'pdo_mysql',
            'host' => $db->getHost(),
            'username' => $db->getUser(),
            'password' => $db->getPassword(),
            'dbname' => $db->getSchema(),
            'active' => true,
        ];
        /** @var \Magento\Framework\Model\Resource\Type\Db\Pdo\Mysql $object */
        $object = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Model\Resource\Type\Db\Pdo\Mysql',
            ['config' => $config]
        );

        $connection = $object->getConnection();
        $this->assertInstanceOf('\Magento\Framework\DB\Adapter\Pdo\Mysql', $connection);
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf('\Magento\Framework\DB\Profiler', $profiler);
    }
}

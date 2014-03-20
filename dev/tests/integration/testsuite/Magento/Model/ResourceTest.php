<?php
/**
 * Test for \Magento\Model\Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Resource
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\App\Resource');
    }

    public function testGetTableName()
    {
        $tablePrefix = 'prefix_';
        $tableSuffix = 'suffix';
        $tableNameOrig = 'core_website';

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\App\Resource',
            array('tablePrefix' => 'prefix_')
        );

        $tableName = $this->_model->getTableName(array($tableNameOrig, $tableSuffix));
        $this->assertContains($tablePrefix, $tableName);
        $this->assertContains($tableSuffix, $tableName);
        $this->assertContains($tableNameOrig, $tableName);
    }

    /**
     * Init profiler during creation of DB connect
     */
    public function testProfilerInit()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Zend_Db_Adapter_Abstract $connection */
        $connection = $objectManager->create(
            'Magento\TestFramework\Db\Adapter\Mysql',
            array(
                'config' => array(
                    'profiler' => array(
                        'class' => 'Magento\Model\Resource\Db\Profiler',
                        'enabled' => 'true'
                    ),
                    'username' => 'username',
                    'password' => 'password',
                    'host' => 'host',
                    'type' => 'type',
                    'dbname' => 'dbname'
                )
            )
        );

        /** @var \Magento\Model\Resource\Db\Profiler $profiler */
        $profiler = $connection->getProfiler();

        $this->assertInstanceOf('Magento\Model\Resource\Db\Profiler', $profiler);
        $this->assertTrue($profiler->getEnabled());
    }
}

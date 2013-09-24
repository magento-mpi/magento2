<?php
/**
 * Test for Magento_Core_Model_Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Resource');
    }

    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testGetTableName()
    {
        $tablePrefix = 'prefix_';
        $tableSuffix = 'suffix';
        $tableNameOrig = 'core_website';

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
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var Zend_Db_Adapter_Abstract $connection */
        $connection = $objectManager->create(
            'Magento_TestFramework_Db_Adapter_Mysql',
            array(
                'profiler' => array(
                    'class' => 'Magento_Core_Model_Resource_Db_Profiler',
                    'enabled' => 'true'
                ),
                'host' => 'host',
                'type' => 'type'
            )
        );

        /** @var Magento_Core_Model_Resource_Db_Profiler $profiler */
        $profiler = $connection->getProfiler();

        $this->assertInstanceOf('Magento_Core_Model_Resource_Db_Profiler', $profiler);
        $this->assertTrue($profiler->getEnabled());
        $this->assertEquals($profiler->getHost(), 'host');
        $this->assertEquals($profiler->getType(), 'type');
    }
}

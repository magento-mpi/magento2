<?php
/**
 * Test for \Magento\Core\Model\Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento\Core\Model\Resource');
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
        $connReadConfig = Mage::getSingleton('Magento\Core\Model\Config\Resource')
            ->getResourceConnectionConfig('core_read');
        $profilerConfig = $connReadConfig->addChild('profiler');
        $profilerConfig->addChild('class', 'Magento\Core\Model\Resource\Db\Profiler');
        $profilerConfig->addChild('enabled', 'true');

        /** @var Zend_Db_Adapter_Abstract $connection */
        $connection = $this->_model->getConnection('core_read');
        /** @var \Magento\Core\Model\Resource\Db\Profiler $profiler */
        $profiler = $connection->getProfiler();

        $this->assertInstanceOf('Magento\Core\Model\Resource\Db\Profiler', $profiler);
        $this->assertTrue($profiler->getEnabled());
        $this->assertAttributeEquals((string)$connReadConfig->host, '_host', $profiler);
        $this->assertAttributeEquals((string)$connReadConfig->type, '_type', $profiler);
    }
}

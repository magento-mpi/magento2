<?php
/**
 * Test for Magento_Core_Model_Resource_Db_Profiler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Db_ProfilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_model;

    /**
     * @var string
     */
    protected static $_testResourceName = 'testtest_0000_setup';

    public static function setUpBeforeClass()
    {
        self::$_testResourceName = 'testtest_' . mt_rand(1000, 9999) . '_setup';

        Magento_Profiler::enable();
    }

    public static function tearDownAfterClass()
    {
        Magento_Profiler::disable();
    }

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Resource');
    }

    /**
     * @return Magento_Simplexml_Element
     */
    protected function _getConnectionReadConfig()
    {
        $connReadConfig = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Config_Resource')
            ->getResourceConnectionConfig('core_read');
        $profilerConfig = $connReadConfig->addChild('profiler');
        $profilerConfig->addChild('class', 'Magento_Core_Model_Resource_Db_Profiler');
        $profilerConfig->addChild('enabled', 'true');

        return $connReadConfig;
    }

    /**
     * Init profiler during creation of DB connect
     *
     * @param string $selectQuery
     * @param int $queryType
     * @dataProvider profileQueryDataProvider
     */
    public function testProfilerInit($selectQuery, $queryType)
    {
        $connReadConfig = $this->_getConnectionReadConfig();
        /** @var Magento_Test_Db_Adapter_Mysql $connection */
        $connection = $this->_model->getConnection('core_read');

        /** @var Magento_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('Magento_Core_Model_Resource');
        $testTableName = $resource->getTableName('core_resource');
        $selectQuery = sprintf($selectQuery, $testTableName);

        $result = $connection->query($selectQuery);
        if ($queryType == Zend_Db_Profiler::SELECT) {
            $result->fetchAll();
        }

        /** @var Magento_Core_Model_Resource_Db_Profiler $profiler */
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf('Magento_Core_Model_Resource_Db_Profiler', $profiler);
        $this->assertAttributeEquals((string)$connReadConfig->type, '_type', $profiler);

        $queryProfiles = $profiler->getQueryProfiles($queryType);
        $this->assertCount(1, $queryProfiles);

        /** @var Zend_Db_Profiler_Query $queryProfile */
        $queryProfile = end($queryProfiles);
        $this->assertInstanceOf('Zend_Db_Profiler_Query', $queryProfile);

        $this->assertEquals($selectQuery, $queryProfile->getQuery());
    }

    /**
     * @return array
     */
    public function profileQueryDataProvider()
    {
        return array(
            array("SELECT * FROM %s", Magento_DB_Profiler::SELECT),
            array("INSERT INTO %s (code, version, data_version) "
                . "VALUES ('" . self::$_testResourceName . "', '1.1', '1.1')", Magento_DB_Profiler::INSERT),
            array("UPDATE %s SET version = '1.2' WHERE code = '" . self::$_testResourceName . "'",
                Magento_DB_Profiler::UPDATE),
            array("DELETE FROM %s WHERE code = '" . self::$_testResourceName . "'",
                Magento_DB_Profiler::DELETE),
        );
    }

    /**
     * Test correct event starting and stopping in magento profile during SQL query fail
     */
    public function testProfilerDuringSqlException()
    {
        /** @var Zend_Db_Adapter_Pdo_Abstract $connection */
        $connection = $this->_model->getConnection('core_read');

        try {
            $connection->query('SELECT * FROM unknown_table');
        } catch (Zend_Db_Statement_Exception $exception) {
        }

        if (!isset($exception)) {
            $this->fail("Expected exception didn't thrown!");
        }

        /** @var Magento_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('Magento_Core_Model_Resource');
        $testTableName = $resource->getTableName('core_resource');
        $connection->query('SELECT * FROM ' . $testTableName);

        /** @var Magento_Core_Model_Resource_Db_Profiler $profiler */
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf('Magento_Core_Model_Resource_Db_Profiler', $profiler);

        $queryProfiles = $profiler->getQueryProfiles(Magento_DB_Profiler::SELECT);
        $this->assertCount(2, $queryProfiles);
    }
}

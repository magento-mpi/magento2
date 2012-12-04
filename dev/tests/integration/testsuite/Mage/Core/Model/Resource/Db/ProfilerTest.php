<?php
/**
 * Test for Mage_Core_Model_Resource_Db_Profiler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Resource_Db_ProfilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource
     */
    protected $_model;

    /**
     * @var string
     */
    protected static $_testResourceName;

    public static function setUpBeforeClass()
    {
        self::$_testResourceName = 'testtest_' . mt_rand(1000, 9999) . '_setup';
    }

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Resource');
    }

    protected function tearDown()
    {
        $this->_model = null;
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
        Magento_Profiler::enable();

        $connReadConfig = Mage::getConfig()->getResourceConnectionConfig('core_read');
        $profilerConfig = $connReadConfig->addChild('profiler');
        $profilerConfig->addChild('class', 'Mage_Core_Model_Resource_Db_Profiler');
        $profilerConfig->addChild('enabled', 'true');

        /** @var Magento_Test_Db_Adapter_Mysql $connection */
        $connection = $this->_model->getConnection('core_read');

        $result = $connection->query($selectQuery);
        if ($queryType == Zend_Db_Profiler::SELECT) {
            $result->fetchAll();
        }

        /** @var Mage_Core_Model_Resource_Db_Profiler $profiler */
        $profiler = $connection->getProfiler();
        $this->assertInstanceOf('Mage_Core_Model_Resource_Db_Profiler', $profiler);
        $this->assertAttributeEquals((string)$connReadConfig->type, '_type', $profiler);

        $queryProfiles = $profiler->getQueryProfiles($queryType);
        $this->assertCount(1, $queryProfiles);

        /** @var Zend_Db_Profiler_Query $queryProfile */
        $queryProfile = end($queryProfiles);
        $this->assertInstanceOf('Zend_Db_Profiler_Query', $queryProfile);

        $this->assertEquals($selectQuery, $queryProfile->getQuery());
    }

    public function profileQueryDataProvider()
    {
        return array(
            array("SELECT * FROM core_resource", Zend_Db_Profiler::SELECT),
            array("INSERT INTO core_resource (code, version, data_version) "
                . "VALUES ('" . self::$_testResourceName . "', '1.1', '1.1')", Zend_Db_Profiler::INSERT),
            array("UPDATE core_resource SET version = '1.2' WHERE code = '" . self::$_testResourceName . "'",
                Zend_Db_Profiler::UPDATE),
            array("DELETE FROM core_resource WHERE code = '" . self::$_testResourceName . "'",
                Zend_Db_Profiler::DELETE),
        );
    }

}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Resource_Product_Option_ValueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value
     */
    protected $_object;

    /**
     * Option value title data
     *
     * @var array
     */
    public static $valueTitleData = array(
        'id'       => 2,
        'store_id' => Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID,
        'scope'    => array('title' => 1)
    );

    protected function setUp()
    {
        $this->_object = new Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value();
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Test that there is no notice in _saveValueTitles()
     *
     * @covers Magento_Catalog_Model_Resource_Product_Option_Value::_saveValueTitles
     */
    public function testSaveValueTitles()
    {
        $object = new Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_Magento_Core_Model_Stub(
            $this->getMock('Magento_Core_Model_Context', array(), array(), '', false),
            null,
            null,
            self::$valueTitleData
        );

        $this->_object->saveValueTitles($object);
    }
}

class Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value
    extends Magento_Catalog_Model_Resource_Product_Option_Value
{
    /**
     * Stub parent constructor
     */
    public function __construct()
    {
        $this->_connections = array(
            'read' =>
                new Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_\Magento\DB\Adapter\Pdo\Mysql(),
            'write' =>
                new Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_\Magento\DB\Adapter\Pdo\Mysql(),
        );
    }

    /**
     * Save option value price data
     *
     * @param Magento_Core_Model_Abstract $object
     */
    public function saveValueTitles(Magento_Core_Model_Abstract $object)
    {
        $this->_saveValueTitles($object);
    }

    /**
     * We should stub to not use db
     *
     * @param string $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        return $tableName;
    }
}

/*
 * Extend \Magento\DB\Adapter\Pdo\Mysql and stub needed methods
 */
class Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_\Magento\DB\Adapter\Pdo\Mysql
    extends \Magento\DB\Adapter\Pdo\Mysql
{
    /**
     * Disable parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Stub delete method and add needed asserts
     *
     * @param  string $table
     * @param  array|string $where
     * @return int
     */
    public function delete($table, $where = '')
    {
        PHPUnit_Framework_TestCase::assertEquals('catalog_product_option_type_title', $table);
        PHPUnit_Framework_TestCase::assertInternalType('array', $where);
        PHPUnit_Framework_TestCase::assertEquals(
            Magento_Catalog_Model_Resource_Product_Option_ValueTest::$valueTitleData['id'],
            $where['option_type_id = ?']
        );
        PHPUnit_Framework_TestCase::assertEquals(
            Magento_Catalog_Model_Resource_Product_Option_ValueTest::$valueTitleData['store_id'],
            $where['store_id = ?']
        );

        return 0;
    }
}

/*
 * Because Magento_Core_Model_Abstract is abstract - we can't instantiate it directly
 */
class Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_Magento_Core_Model_Stub
    extends Magento_Core_Model_Abstract
{
}

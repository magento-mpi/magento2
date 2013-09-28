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

namespace Magento\Catalog\Model\Resource\Product\Option;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value
     */
    protected $_object;

    /**
     * Option value title data
     *
     * @var array
     */
    public static $valueTitleData = array(
    'id'       => 2,
    'store_id' => \Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID,
    'scope'    => array('title' => 1)
    );

    protected function setUp()
    {
        $this->_object = new \Magento\Catalog\Model\Resource\Product\Option\ValueStub();
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Test that there is no notice in _saveValueTitles()
     *
     * @covers \Magento\Catalog\Model\Resource\Product\Option\Value::_saveValueTitles
     */
    public function testSaveValueTitles()
    {
        $object = new Stub(
            $this->getMock('Magento\Core\Model\Context', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            null,
            null,
            self::$valueTitleData
        );

        $this->_object->saveValueTitles($object);
    }
}

class Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value
    extends \Magento\Catalog\Model\Resource\Product\Option\Value
{
    /**
     * Stub parent constructor
     */
    public function __construct()
    {
        $this->_connections = array(
            'read' =>
                new Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_Magento_DB_Adapter_Pdo_Mysql(),
            'write' =>
                new Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_Magento_DB_Adapter_Pdo_Mysql(),
        );
    }

    /**
     * Save option value price data
     *
     * @param \Magento\Core\Model\AbstractModel $object
     */
    public function saveValueTitles(\Magento\Core\Model\AbstractModel $object)
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
class Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_Magento_DB_Adapter_Pdo_Mysql
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
        \PHPUnit_Framework_TestCase::assertEquals('catalog_product_option_type_title', $table);
        \PHPUnit_Framework_TestCase::assertInternalType('array', $where);
        \PHPUnit_Framework_TestCase::assertEquals(
            Magento_Catalog_Model_Resource_Product_Option_ValueTest::$valueTitleData['id'],
            $where['option_type_id = ?']
        );
        \PHPUnit_Framework_TestCase::assertEquals(
            Magento_Catalog_Model_Resource_Product_Option_ValueTest::$valueTitleData['store_id'],
            $where['store_id = ?']
        );

        return 0;
    }
}

/*
 * Because \Magento\Core\Model\AbstractModel is abstract - we can't instantiate it directly
 */
class Stub_UnitTest_Magento_Catalog_Model_Resource_Product_Option_Value_Magento_Core_Model_Stub
    extends \Magento\Core\Model\AbstractModel
{
}

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

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value
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
        $this->_object = new \Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value();
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
        $object = new \Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value\Magento\Core\Model\Stub(
            $this->getMock('Magento\Core\Model\Context', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            null,
            null,
            self::$valueTitleData
        );

        $this->_object->saveValueTitles($object);
    }
}

class Value
    extends \Magento\Catalog\Model\Resource\Product\Option\Value
{
    /**
     * Stub parent constructor
     */
    public function __construct()
    {
        $this->_connections = array(
            'read' =>
                new \Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value\Magento\DB\Adapter\Pdo\Mysql(),
            'write' =>
                new \Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value\Magento\DB\Adapter\Pdo\Mysql(),
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
class Mysql
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
            \Magento\Catalog\Model\Resource\Product\Option\ValueTest::$valueTitleData['id'],
            $where['option_type_id = ?']
        );
        \PHPUnit_Framework_TestCase::assertEquals(
            \Magento\Catalog\Model\Resource\Product\Option\ValueTest::$valueTitleData['store_id'],
            $where['store_id = ?']
        );

        return 0;
    }
}

/*
 * Because \Magento\Core\Model\AbstractModel is abstract - we can't instantiate it directly
 */
namespace Magento\Catalog\Model\Resource\Product\Option;

namespace Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option;

namespace Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value\Magento\DB\Adapter\Pdo;

namespace Stub\UnitTest\Magento\Catalog\Model\Resource\Product\Option\Value\Magento\Core\Model;

class Stub
    extends \Magento\Core\Model\AbstractModel
{
}

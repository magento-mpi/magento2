<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Db
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for an environment-dependent DB adapter that implements Varien_Db_Adapter_Interface
 */
class Varien_Db_Adapter_InterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * @var string
     */
    protected $_tableName = '_two_column_idx';

    /**
     * @var string
     */
    protected $_oneColumnIdxName;

    /**
     * @var string
     */
    protected $_twoColumnIdxName;

    protected function setUp()
    {
        $installer = new Mage_Core_Model_Resource_Setup(Mage_Core_Model_Resource_Setup::DEFAULT_SETUP_CONNECTION);
        $this->_connection = $installer->getConnection();
        $this->_oneColumnIdxName = $this->_connection->getIndexName($this->_tableName, array('column1'));
        $this->_twoColumnIdxName = $this->_connection->getIndexName($this->_tableName, array('column1', 'column2'));
    }

    /**
     * Cleanup DDL cache for the fixture table
     */
    protected function tearDown()
    {
        $this->_connection->resetDdlCache($this->_tableName);
    }

    protected function assertPreConditions()
    {
        $this->assertTrue(
            $this->_connection->tableColumnExists($this->_tableName, 'column1'),
            'Table column "column1" must be provided by the fixture.'
        );
        $this->assertTrue(
            $this->_connection->tableColumnExists($this->_tableName, 'column2'),
            'Table column "column2" must be provided by the fixture.'
        );
        $this->assertEquals(
            array('column1'),
            $this->_getIndexColumns($this->_tableName, $this->_oneColumnIdxName),
            'Single-column index must be provided by the fixture.'
        );
        $this->assertEquals(
            array('column1', 'column2'),
            $this->_getIndexColumns($this->_tableName, $this->_twoColumnIdxName),
            'Multiple-column index must be provided by the fixture.'
        );
    }

    /**
     * Retrieve list of columns used for an index or return false, if an index with a given name does not exist
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|null $schemaName
     * @return array|false
     */
    protected function _getIndexColumns($tableName, $indexName, $schemaName = null)
    {
        foreach ($this->_connection->getIndexList($tableName, $schemaName) as $idxData) {
            if ($idxData['KEY_NAME'] == $indexName) {
                return $idxData['COLUMNS_LIST'];
            }
        }
        return false;
    }

    /**
     * @magentoDataFixture Varien/Db/Adapter/_files/table_two_column_idx.php
     */
    public function testDropColumn()
    {
        $this->_connection->dropColumn($this->_connection->getTableName($this->_tableName), 'column1');
        $this->assertFalse(
            $this->_connection->tableColumnExists($this->_tableName, 'column1'),
            'Table column must not exist after it has been dropped.'
        );
    }

    /**
     * @magentoDataFixture Varien/Db/Adapter/_files/table_two_column_idx.php
     */
    public function testDropColumnRemoveFromIndexes()
    {
        $this->_connection->dropColumn($this->_connection->getTableName($this->_tableName), 'column1');
        $this->assertFalse(
            $this->_getIndexColumns($this->_tableName, $this->_oneColumnIdxName),
            'Column index must be dropped along with the column.'
        );
        $this->assertEquals(
            array('column2'),
            $this->_getIndexColumns($this->_tableName, $this->_twoColumnIdxName),
            'References to the dropped column must be removed from the multiple-column indexes.'
        );
    }

    /**
     * @magentoDataFixture Varien/Db/Adapter/_files/table_two_column_idx.php
     */
    public function testDropColumnSameColumnIndexDuplicate()
    {
        $this->_connection->dropColumn($this->_connection->getTableName($this->_tableName), 'column2');
        $this->assertEquals(
            array('column1'),
            $this->_getIndexColumns($this->_tableName, $this->_oneColumnIdxName),
            'Column index must be preserved.'
        );
        $this->assertEquals(
            array('column1'),
            $this->_getIndexColumns($this->_tableName, $this->_twoColumnIdxName),
            'Multiple-column index must be re-created with no reference to the dropped column.'
        );
    }
}

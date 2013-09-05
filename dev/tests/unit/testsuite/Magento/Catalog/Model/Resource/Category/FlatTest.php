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

class Magento_Catalog_Model_Resource_Category_FlatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\DB\Adapter\Pdo\Mysql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbAdapterMock;

    protected function setUp()
    {
        $this->_dbAdapterMock = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);
    }

    /**
     * @param array $methods
     * @return Magento_Catalog_Model_Resource_Category_Flat|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getModelMock(array $methods = array())
    {
        return $this->getMockBuilder('Magento_Catalog_Model_Resource_Category_Flat')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCreateTableDoesNotInvokeDdlOperationsIfTheyAreNotAllowed()
    {
        $model = $this->_getModelMock(array('_createTable', '_getWriteAdapter'));

        // Pretend that some transaction has been started
        $this->_dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(1));
        $model->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($this->_dbAdapterMock));

        $model->expects($this->never())->method('_createTable');
        $model->createTable(1);
    }

    public function testCreateTableInvokesDdlOperationsIfTheyAreAllowed()
    {
        $model = $model = $this->_getModelMock(array('_createTable', '_getWriteAdapter'));

        // Pretend that no transactions have been started
        $this->_dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(0));
        $model->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($this->_dbAdapterMock));

        $model->expects($this->atLeastOnce())->method('_createTable');
        $model->createTable(1);
    }

    public function testReindexAllCreatesFlatTablesAndInvokesRebuildProcessWithoutArguments()
    {
        $model = $this->_getModelMock(array('_createTables', 'rebuild', 'commit', 'beginTransaction', 'rollBack'));
        $model->expects($this->once())->method('_createTables');
        $model->expects($this->once())->method('rebuild')->with($this->isNull());
        $model->reindexAll();
    }

    public function testRebuildDoesNotInvokeDdlOperationsIfTheyAreNotAllowed()
    {
        $model = $this->_getModelMock(array('_createTable', '_getWriteAdapter', '_populateFlatTables'));

        // Pretend that some transaction has been started
        $this->_dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(1));
        $model->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($this->_dbAdapterMock));

        $model->expects($this->never())->method('_createTable');

        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())->method('getId')->will($this->returnValue(1));

        $model->rebuild(array($store));
    }

    public function testRebuildInvokesDdlOperationsIfTheyAreAllowed()
    {
        $model = $this->_getModelMock(array('_createTable', '_getWriteAdapter', '_populateFlatTables'));

        // Pretend that no transactions have been started
        $this->_dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(0));
        $model->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($this->_dbAdapterMock));

        $model->expects($this->atLeastOnce())->method('_createTable');

        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())->method('getId')->will($this->returnValue(1));

        $model->rebuild(array($store));
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows;

class TableDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_connectionMock;

    /**
     * @var \Magento\Catalog\Helper\Product\Flat\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productIndexerHelper;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->_resourceMock = $this->getMock('Magento\Framework\App\Resource', array(), array(), '', false);
        $this->_productIndexerHelper = $this->getMock(
            'Magento\Catalog\Helper\Product\Flat\Indexer',
            array(),
            array(),
            '',
            false
        );
    }

    public function testMoveWithNonExistentFlatTable()
    {
        $flatTable = 'flat_table';
        $flatDropName = 'flat_table_to_drop';
        $temporaryFlatTableName = 'flat_tmp';

        $this->_connectionMock->expects($this->exactly(2))->method('dropTable')->with($flatDropName);
        $this->_connectionMock->expects(
            $this->once()
        )->method(
            'isTableExists'
        )->with(
            $flatTable
        )->will(
            $this->returnValue(false)
        );

        $this->_connectionMock->expects(
            $this->once()
        )->method(
            'renameTablesBatch'
        )->with(
            array('oldName' => 'flat_tmp', 'newName' => 'flat_table')
        );

        $this->_resourceMock->expects(
            $this->once()
        )->method(
            'getConnection'
        )->with(
            'write'
        )->will(
            $this->returnValue($this->_connectionMock)
        );

        $model = $this->_objectManager->getObject(
            'Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows\TableData',
            array('resource' => $this->_resourceMock, 'productIndexerHelper' => $this->_productIndexerHelper)
        );

        $model->move($flatTable, $flatDropName, $temporaryFlatTableName);
    }

    public function testMoveWithExistentFlatTable()
    {
        $flatTable = 'flat_table';
        $flatDropName = 'flat_table_to_drop';
        $temporaryFlatTableName = 'flat_tmp';

        $describedColumns = array(
            'column_11' => 'column_definition',
            'column_2' => 'column_definition',
            'column_3' => 'column_definition'
        );

        $flatColumns = array(
            'column_1' => 'column_definition',
            'column_2' => 'column_definition',
            'column_3' => 'column_definition'
        );

        $selectMock = $this->getMock('Magento\Framework\DB\Select', array(), array(), '', false);
        $selectMock->expects(
            $this->once()
        )->method(
            'from'
        )->with(
            array('tf' => sprintf('%s_tmp_indexer', $flatTable)),
            array('column_2', 'column_3')
        );
        $sql = md5(time());
        $selectMock->expects(
            $this->once()
        )->method(
            'insertFromSelect'
        )->with(
            $flatTable,
            array('column_2', 'column_3')
        )->will(
            $this->returnValue($sql)
        );

        $this->_connectionMock->expects($this->once())->method('query')->with($sql);

        $this->_connectionMock->expects($this->once())->method('select')->will($this->returnValue($selectMock));

        $this->_connectionMock->expects(
            $this->once()
        )->method(
            'isTableExists'
        )->with(
            $flatTable
        )->will(
            $this->returnValue(true)
        );

        $this->_connectionMock->expects(
            $this->once()
        )->method(
            'describeTable'
        )->with(
            $flatTable
        )->will(
            $this->returnValue($describedColumns)
        );

        $this->_productIndexerHelper->expects(
            $this->once()
        )->method(
            'getFlatColumns'
        )->will(
            $this->returnValue($flatColumns)
        );

        $this->_connectionMock->expects(
            $this->once()
        )->method(
            'dropTable'
        )->with(
            sprintf('%s_tmp_indexer', $flatTable)
        );

        $this->_resourceMock->expects(
            $this->any()
        )->method(
            'getConnection'
        )->with(
            'write'
        )->will(
            $this->returnValue($this->_connectionMock)
        );

        $model = $this->_objectManager->getObject(
            'Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows\TableData',
            array('resource' => $this->_resourceMock, 'productIndexerHelper' => $this->_productIndexerHelper)
        );

        $model->move($flatTable, $flatDropName, $temporaryFlatTableName);
    }
}

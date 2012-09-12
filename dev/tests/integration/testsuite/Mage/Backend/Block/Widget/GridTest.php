<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    protected $_block;

    protected $_columnSetMock;

    protected function setUp()
    {
        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $this->_columnSetMock = $this->getMock('Mage_Backend_Block_Widget_Grid_ColumnSet');
        $this->_layoutMock->expects($this->any())->method('getChildName')
            ->with('grid', 'grid.columnSet')
            ->will($this->returnValue('grid.columnSet'));
        $this->_layoutMock->expects($this->any())->method('getBlock')
            ->with('grid.columnSet')
            ->will($this->returnValue($this->_columnSetMock));
        $this->_block = new Mage_Backend_Block_Widget_Grid(array('layout' => $this->_layoutMock));
        $this->_block->setNameInLayout('grid');
    }

    public function testToHtmlPreparesColumns()
    {
        $this->_columnSetMock->expects($this->once())->method('setRendererType');
        $this->_columnSetMock->expects($this->once())->method('setFilterType');
        $this->_columnSetMock->expects($this->once())->method('setSortable');
        $this->_block->setColumnRenderers(array('filter' => 'Filter_Class'));
        $this->_block->setColumnFilters(array('filter' => 'Filter_Class'));
        $this->_block->setSortable(false);
        $this->_block->toHtml();
    }
}

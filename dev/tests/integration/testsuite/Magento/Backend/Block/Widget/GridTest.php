<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_ColumnSet
     */
    protected $_block;

    /**
     * @var Magento_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var Magento_Backend_Block_Widget_Grid_ColumnSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnSetMock;

    protected function setUp()
    {
        $this->_layoutMock = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
        $this->_columnSetMock = $this->_getColumnSetMock();

        $returnValueMap = array(
            array('grid', 'grid.columnSet', 'grid.columnSet'),
            array('grid', 'reset_filter_button', 'reset_filter_button'),
            array('grid', 'search_button', 'search_button')
        );
        $this->_layoutMock->expects($this->any())->method('getChildName')
            ->will($this->returnValueMap($returnValueMap));
        $this->_layoutMock->expects($this->any())->method('getBlock')
            ->with('grid.columnSet')
            ->will($this->returnValue($this->_columnSetMock));
        $this->_layoutMock->expects($this->any())->method('createBlock')
            ->with('Magento_Backend_Block_Widget_Button')
            ->will($this->returnValue(Mage::app()->getLayout()->createBlock('Magento_Backend_Block_Widget_Button')));
        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Magento_Core_Helper_Data')
            ->will($this->returnValue(
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Helper_Data')
            ));


        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Backend_Block_Widget_Grid');
        $this->_block->setLayout($this->_layoutMock);
        $this->_block->setNameInLayout('grid');
    }

    /**
     * Retrieve the mocked column set block instance
     *
     * @return Magento_Backend_Block_Widget_Grid_ColumnSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getColumnSetMock()
    {
        return $this->getMock('Magento_Backend_Block_Widget_Grid_ColumnSet', array(), array(
            $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false),
            Mage::getModel('Magento_Core_Block_Template_Context', array(
                'dirs' => new Magento_Core_Model_Dir(__DIR__),
                'filesystem' => new Magento_Filesystem(new Magento_Filesystem_Adapter_Local),
            )),
            Mage::getModel('Magento_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory'),
            Mage::getModel('Magento_Backend_Model_Widget_Grid_SubTotals'),
            Mage::getModel('Magento_Backend_Model_Widget_Grid_Totals'),
        ));
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

    public function testGetMainButtonsHtmlReturnsEmptyStringIfFiltersArentVisible()
    {
        $this->_columnSetMock->expects($this->once())->method('isFilterVisible')->will($this->returnValue(false));
        $this->_block->getMainButtonsHtml();
    }
}

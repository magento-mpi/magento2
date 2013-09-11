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
     * @var \Magento\Backend\Block\Widget\Grid\ColumnSet
     */
    protected $_block;

    /**
     * @var \Magento\Core\Model\Layout|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\ColumnSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnSetMock;

    protected function setUp()
    {
        $this->_layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
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
            ->with('Magento\Backend\Block\Widget\Button')
            ->will($this->returnValue(Mage::app()->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button')));
        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue(Mage::helper('Magento\Core\Helper\Data')));


        $this->_block = Mage::app()->getLayout()->createBlock('\Magento\Backend\Block\Widget\Grid');
        $this->_block->setLayout($this->_layoutMock);
        $this->_block->setNameInLayout('grid');
    }

    /**
     * Retrieve the mocked column set block instance
     *
     * @return \Magento\Backend\Block\Widget\Grid\ColumnSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getColumnSetMock()
    {
        return $this->getMock('Magento\Backend\Block\Widget\Grid\ColumnSet', array(), array(
            Mage::getModel('\Magento\Core\Block\Template\Context', array(
                'dirs' => new \Magento\Core\Model\Dir(__DIR__),
                'filesystem' => new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local),
            )),
            Mage::getModel('\Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory'),
            Mage::getModel('\Magento\Backend\Model\Widget\Grid\SubTotals'),
            Mage::getModel('\Magento\Backend\Model\Widget\Grid\Totals'),
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

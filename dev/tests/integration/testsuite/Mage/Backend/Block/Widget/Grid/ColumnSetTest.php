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

class Mage_Backend_Block_Widget_Grid_ColumnSetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnMock;

    protected function setUp()
    {
        $this->_columnMock = $this->getMock('Mage_Backend_Block_Widget_Grid_Column',
            array('setSortable', 'setRendererType', 'setFilterType', 'addHeaderCssClass', 'setGrid'),
            array(), '', false
        );
        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $this->_layoutMock->expects($this->any())->method('getChildBlocks')->will($this->returnValue(
            array($this->_columnMock)
        ));

        $this->_block = new Mage_Backend_Block_Widget_Grid_ColumnSet(array('layout' => $this->_layoutMock));
    }

    public function testBeforeToHtmlAddsClassToLastColumn()
    {
        $this->_columnMock->expects($this->any())->method('addHeaderCssClass')->with($this->equalTo('last'));
        $this->_block->toHtml();
    }
}

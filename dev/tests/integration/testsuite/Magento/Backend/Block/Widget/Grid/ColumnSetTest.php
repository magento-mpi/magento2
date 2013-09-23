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
class Magento_Backend_Block_Widget_Grid_ColumnSetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_ColumnSet
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
        parent::setUp();

        $this->_columnMock = $this->getMock('Magento_Backend_Block_Widget_Grid_Column',
            array('setSortable', 'setRendererType', 'setFilterType', 'addHeaderCssClass', 'setGrid'),
            array(), '', false
        );
        $this->_layoutMock = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
        $this->_layoutMock->expects($this->any())->method('getChildBlocks')->will($this->returnValue(
            array($this->_columnMock)
        ));

        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Block_Template_Context', array('layout' => $this->_layoutMock));
        $this->_block = Mage::app()->getLayout()->createBlock(
            'Magento_Backend_Block_Widget_Grid_ColumnSet', '', array('context' => $context)
        );
        $this->_block->setTemplate(null);
    }

    public function testBeforeToHtmlAddsClassToLastColumn()
    {
        $this->_columnMock->expects($this->any())->method('addHeaderCssClass')->with($this->equalTo('last'));
        $this->_block->toHtml();
    }
}

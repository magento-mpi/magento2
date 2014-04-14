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
namespace Magento\Backend\Block\Widget\Grid;

/**
 * @magentoAppArea adminhtml
 */
class ColumnSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\Widget\Grid\ColumnSet
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnMock;

    protected function setUp()
    {
        parent::setUp();

        $this->_columnMock = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Column',
            array('setSortable', 'setRendererType', 'setFilterType', 'addHeaderCssClass', 'setGrid'),
            array(),
            '',
            false
        );
        $this->_layoutMock = $this->getMock('Magento\View\Layout', array(), array(), '', false);
        $this->_layoutMock->expects(
            $this->any()
        )->method(
            'getChildBlocks'
        )->will(
            $this->returnValue(array($this->_columnMock))
        );

        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\View\Element\Template\Context',
            array('layout' => $this->_layoutMock)
        );
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Backend\Block\Widget\Grid\ColumnSet',
            '',
            array('context' => $context)
        );
        $this->_block->setTemplate(null);
    }

    public function testBeforeToHtmlAddsClassToLastColumn()
    {
        $this->_columnMock->expects($this->any())->method('addHeaderCssClass')->with($this->equalTo('last'));
        $this->_block->toHtml();
    }
}

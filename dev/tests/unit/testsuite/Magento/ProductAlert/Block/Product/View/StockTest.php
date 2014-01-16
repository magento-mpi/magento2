<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Block\Product\View;

/**
 * Test class for \Magento\ProductAlert\Block\Product\View\Stock
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\ProductAlert\Helper\Data
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\ProductAlert\Block\Product\View\Stock
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Layout
     */
    protected $_layout;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $this->getMock(
            'Magento\ProductAlert\Helper\Data', array('isStockAlertAllowed', 'getSaveUrl'), array(), '', false
        );
        $this->_product = $this->getMock(
            'Magento\Catalog\Model\Product', array('isAvailable', 'getId', '__wakeup'), array(), '', false
        );
        $this->_product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->_registry = $this->getMockBuilder('Magento\Core\Model\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $this->_block = $objectManager->getObject(
            'Magento\ProductAlert\Block\Product\View\Stock',
            array(
                'helper' => $this->_helper,
                'registry' => $this->_registry,
            )
        );
        $this->_layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
    }

    public function testSetTemplateStockUrlAllowed()
    {
        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $this->_helper->expects($this->once())
            ->method('getSaveUrl')
            ->with('stock')
            ->will($this->returnValue('http://url'));

        $this->_product->expects($this->once())->method('isAvailable')->will($this->returnValue(false));

        $this->_registry->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($this->_product));

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('path/to/template.phtml', $this->_block->getTemplate());
        $this->assertEquals('http://url', $this->_block->getSignupUrl());
    }

    /**
     * @param bool $stockAlertAllowed
     * @param bool $productAvailable
     * @dataProvider setTemplateStockUrlNotAllowedDataProvider
     */
    public function testSetTemplateStockUrlNotAllowed($stockAlertAllowed, $productAvailable)
    {
        $this->_helper
            ->expects($this->once())
            ->method('isStockAlertAllowed')
            ->will($this->returnValue($stockAlertAllowed));
        $this->_helper->expects($this->never())->method('getSaveUrl');

        $this->_product->expects($this->any())->method('isAvailable')->will($this->returnValue($productAvailable));

        $this->_registry->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($this->_product));

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('', $this->_block->getTemplate());
        $this->assertNull($this->_block->getSignupUrl());
    }

    public function setTemplateStockUrlNotAllowedDataProvider()
    {
        return array(
            'stock alert not allowed' => array(false, false),
            'product is available (no alert)' => array(true, true),
            'stock alert not allowed and product is available' => array(false, true),
        );
    }

    public function testSetTemplateNoProduct()
    {
        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $this->_helper->expects($this->never())->method('getSaveUrl');

        $this->_registry->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue(null));

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('', $this->_block->getTemplate());
        $this->assertNull($this->_block->getSignupUrl());
    }
}

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

/**
 * Test class for \Magento\ProductAlert\Block\Product\View\Price
 */
class Magento_ProductAlert_Block_Product_View_PriceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testPrepareLayoutUrlIsSet()
    {
        $helper = $this->getMockBuilder('Magento\ProductAlert\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isPriceAlertAllowed', 'getSaveUrl'))
            ->getMock();
        $helper->expects($this->once())->method('isPriceAlertAllowed')->will($this->returnValue(true));
        $helper->expects($this->once())->method('getSaveUrl')->with('price')->will($this->returnValue('http://url'));

        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getCanShowPrice', 'getId'))
            ->getMock();
        $product->expects($this->once())->method('getId')->will($this->returnValue(1));
        $product->expects($this->once())->method('getCanShowPrice')->will($this->returnValue(true));

        $registry = $this->getMockBuilder('Magento\Core\Model\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));

        $block = $this->_objectManager->getObject(
            'Magento\ProductAlert\Block\Product\View\Price',
            array(
                'helper' => $helper,
                'registry' => $registry,
            )
        );

        $layout = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $block->setTemplate('path/to/template.phtml');
        $block->setLayout($layout);

        $this->assertEquals('path/to/template.phtml', $block->getTemplate());
        $this->assertEquals('http://url', $block->getSignupUrl());
    }

    public function testPrepareLayoutTemplateReseted()
    {
        $block = $this->_objectManager->getObject('Magento\ProductAlert\Block\Product\View\Price');
        $this->assertEquals('', $block->getTemplate());
    }
}

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
 * Test class for Magento_ProductAlert_Block_Product_View_Stock
 */
class Magento_ProductAlert_Block_Product_View_StockTest extends PHPUnit_Framework_TestCase
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
        $helper = $this->getMockBuilder('Magento_ProductAlert_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isStockAlertAllowed', 'getSaveUrl'))
            ->getMock();
        $helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $helper->expects($this->once())
            ->method('getSaveUrl')
            ->with('stock')
            ->will($this->returnValue('http://url'));

        $product = $this->getMockBuilder('Magento_Catalog_Model_Product')
            ->disableOriginalConstructor()
            ->setMethods(array('isAvailable', 'getId'))
            ->getMock();
        $product->expects($this->once())->method('getId')->will($this->returnValue(1));
        $product->expects($this->once())->method('isAvailable')->will($this->returnValue(false));

        $registry = $this->getMockBuilder('Magento_Core_Model_Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('registry'))
            ->getMock();
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));

        $block = $this->_objectManager->getObject(
            'Magento_ProductAlert_Block_Product_View_Stock',
            array(
                'helper' => $helper,
                'registry' => $registry,
            )
        );

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $block->setTemplate('path/to/template.phtml');
        $block->setLayout($layout);

        $this->assertEquals('path/to/template.phtml', $block->getTemplate());
        $this->assertEquals('http://url', $block->getSignupUrl());
    }

    public function testPrepareLayoutTemplateReseted()
    {
        $block = $this->_objectManager->getObject('Magento_ProductAlert_Block_Product_View_Stock');
        $this->assertEquals('', $block->getTemplate());
    }
}

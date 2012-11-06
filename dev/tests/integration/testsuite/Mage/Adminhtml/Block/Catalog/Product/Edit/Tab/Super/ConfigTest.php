<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetGridJsObject()
    {
        Mage::register('current_product', new Varien_Object);
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config', 'block');
        $this->assertEquals('super_product_linksJsObject', $block->getGridJsObject());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSelectedAttributes()
    {
        $productType = $this->getMock('stdClass', array('getUsedProductAttributes'));
        $product = $this->getMock('Varien_Object', array('getTypeInstance'));

        $product->expects($this->once())->method('getTypeInstance')->will($this->returnValue($productType));
        $productType->expects($this->once())->method('getUsedProductAttributes')->with($this->equalTo($product))
            ->will($this->returnValue(array('', 'a')));

        Mage::register('current_product', $product);
        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config', 'block');
        $this->assertEquals(array(1 => 'a'), $block->getSelectedAttributes());
    }
}

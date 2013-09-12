<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Catalog_Block_Product_List_Crosssell.
 *
 * @magentoDataFixture Magento/Catalog/_files/products_crosssell.php
 */
class Magento_Catalog_Block_Product_List_CrosssellTest extends PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(2);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('product', $product);
        /** @var $block Magento_Catalog_Block_Product_List_Crosssell */
        $block = Mage::app()->getLayout()->createBlock('Magento_Catalog_Block_Product_List_Crosssell');
        $block->setLayout(Mage::getModel('Magento_Core_Model_Layout'));
        $block->setTemplate('Magento_Checkout::cart/crosssell.phtml');
        $block->setItemCount(1);

        $html = $block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertContains('Simple Cross Sell', $html); /* name */
        $this->assertContains('product/1/', $html);  /* part of url */
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Link_Product_Collection', $block->getItems());
    }
}

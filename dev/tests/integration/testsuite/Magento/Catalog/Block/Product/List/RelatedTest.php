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
 * Test class for Magento_Catalog_Block_Product_List_Related.
 *
 * @magentoDataFixture Magento/Catalog/_files/products_related.php
 */
class Magento_Catalog_Block_Product_List_RelatedTest extends PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(2);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('product', $product);
        /** @var $block Magento_Catalog_Block_Product_List_Related */
        $block = Mage::app()->getLayout()->createBlock('Magento_Catalog_Block_Product_List_Related');
        $block->setLayout(Mage::getSingleton('Magento_Core_Model_Layout'));
        $block->setTemplate('product/list/related.phtml');

        $html = $block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertContains('Simple Related Product', $html); /* name */
        $this->assertContains('product/1/', $html);  /* part of url */
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Link_Product_Collection', $block->getItems());
    }
}

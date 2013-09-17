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

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Catalog_Product_Options_AjaxTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_Catalog_Product_Options_Ajax
     */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Options_Ajax');
    }

    public function testToHtmlWithoutProducts()
    {
        $this->assertEquals(json_encode(array()), $this->_block->toHtml());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_with_options.php
     */
    public function testToHtml()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('import_option_products', array(1));
        $result = json_decode($this->_block->toHtml(), true);
        $this->assertEquals('test_option_code_1', $result[0]['title']);
    }
}

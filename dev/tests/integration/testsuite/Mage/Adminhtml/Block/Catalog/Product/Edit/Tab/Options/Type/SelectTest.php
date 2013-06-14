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
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_SelectTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select', 'select');
        $html = $block->getPriceTypeSelectHtml();
        $this->assertContains('select_${select_id}', $html);
        $this->assertContains('[${select_id}]', $html);
    }
}

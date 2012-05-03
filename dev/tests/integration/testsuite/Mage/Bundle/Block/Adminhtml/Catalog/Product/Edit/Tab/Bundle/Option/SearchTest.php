<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_SearchTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasIndex()
    {
        Mage::getDesign()->setArea('adminhtml');
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $block = $layout->createBlock(
            'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search',
            'block');

        $indexValue = 'magento_index_set_to_test';
        $block->setIndex($indexValue);

        $html = $block->toHtml();
        $this->assertContains($indexValue, $html);
    }
}

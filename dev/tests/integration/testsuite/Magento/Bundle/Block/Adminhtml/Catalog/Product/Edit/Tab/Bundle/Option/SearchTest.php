<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_SearchTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasIndex()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Core_Model_Layout',
            array('area' => Magento_Core_Model_App_Area::AREA_ADMINHTML)
        );
        $block = $layout->createBlock(
            'Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search',
            'block2');

        $indexValue = 'magento_index_set_to_test';
        $block->setIndex($indexValue);

        $html = $block->toHtml();
        $this->assertContains($indexValue, $html);
    }
}

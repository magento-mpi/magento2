<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea adminhtml
     */
    public function testGetAfterElementHtml()
    {
        $block = Mage::getObjectManager()->create('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Category');

        $form = new Magento_Data_Form();
        $block->setForm($form);

        $this->assertRegExp('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}

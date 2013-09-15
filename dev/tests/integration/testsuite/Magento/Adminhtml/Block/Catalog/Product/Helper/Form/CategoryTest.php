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

        $form = Mage::getObjectManager()->create('Magento\Data\Form');

        $form = new \Magento\Data\Form();
        $block->setForm($form);

        $this->assertRegExp('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}

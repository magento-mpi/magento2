<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAfterElementHtml()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $layout = Mage::getModel(
            'Magento_Core_Model_Layout',
            array('area' => Magento_Core_Model_App_Area::AREA_ADMINHTML)
        );

        $block = $objectManager->create('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Category',
            array('layout' => $layout));

        /** @var $formFactory Magento_Data_Form_Factory */
        $formFactory = $objectManager->get('Magento_Data_Form_Factory');
        $form = $formFactory->create();
        $block->setForm($form);

        $this->assertRegExp('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAfterElementHtml()
    {
        $layout = Mage::getModel('Mage_Core_Model_Layout', array('area' => Mage_Core_Model_App_Area::AREA_ADMINHTML));

        $block = new Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Category(array(), $layout);

        $form = new Varien_Data_Form();
        $block->setForm($form);

        $html = $block->getAfterElementHtml();
        $pattern = '/<button[^>]*New\sCategory[^>]*>/';
        preg_match($pattern, $html, $matches);
        $this->assertNotEmpty($matches[0]);
    }
}

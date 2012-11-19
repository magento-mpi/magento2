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
 * Test class for Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider virtualTypesProvider
     */
    public function testIsVirtualCheckboxSelected($type)
    {
        $currentProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $currentProduct->setTypeInstance($type);

        $block = new Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer();

        $form = new Varien_Data_Form();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is not selected for virtual products');
    }

    /**
     * @return array
     */
    public function virtualTypesProvider()
    {
        return array(
            array(new Mage_Catalog_Model_Product_Type_Virtual()),
            array(new Mage_Downloadable_Model_Product_Type()),
        );
    }

    /**
     * @dataProvider physicalTypesProvider
     */
    public function testIsVirtualCheckboxUnSelected($type)
    {
        $currentProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $currentProduct->setTypeInstance($type);

        $block = new Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer();

        $form = new Varien_Data_Form();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertNotContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is selected for physical products');
    }

    /**
     * @return array
     */
    public function physicalTypesProvider()
    {
        return array(
            array(new Mage_Catalog_Model_Product_Type_Simple()),
            array(new Mage_Bundle_Model_Product_Type()),
            array(new Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard())
        );
    }
}

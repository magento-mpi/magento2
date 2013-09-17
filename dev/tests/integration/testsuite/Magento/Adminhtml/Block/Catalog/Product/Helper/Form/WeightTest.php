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

class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_WeightTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $type
     * @dataProvider virtualTypesDataProvider
     */
    public function testIsVirtualChecked($type)
    {
        $currentProduct = Mage::getModel('Magento_Catalog_Model_Product');
        $currentProduct->setTypeInstance(Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create($type));

        $block = Mage::getObjectManager()->create('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight');

        $form = Mage::getObjectManager()->create('Magento_Data_Form');
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is not selected for virtual products');
    }

    /**
     * @return array
     */
    public static function virtualTypesDataProvider()
    {
        return array(
            array('Magento_Catalog_Model_Product_Type_Virtual'),
            array('Magento_Downloadable_Model_Product_Type'),
        );
    }

    /**
     * @param string $type
     * @dataProvider physicalTypesDataProvider
     */
    public function testIsVirtualUnchecked($type)
    {
        $currentProduct = Mage::getModel('Magento_Catalog_Model_Product');
        $currentProduct->setTypeInstance(Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create($type));

        $block = Mage::getObjectManager()->create('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight');

        $form = Mage::getObjectManager()->create('Magento_Data_Form');
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertNotContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is selected for physical products');
    }

    /**
     * @return array
     */
    public static function physicalTypesDataProvider()
    {
        return array(
            array('Magento_Catalog_Model_Product_Type_Simple'),
            array('Magento_Bundle_Model_Product_Type'),
        );
    }
}

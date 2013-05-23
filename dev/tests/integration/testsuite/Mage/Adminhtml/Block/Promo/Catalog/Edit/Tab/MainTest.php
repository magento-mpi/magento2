<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main
 */
class Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_MainTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        Mage::register('current_promo_catalog_rule', Mage::getObjectManager()->create('Mage_CatalogRule_Model_Rule'));

        $block = Mage::getObjectManager()->create('Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main');
        $block->setLayout(Mage::getObjectManager()->create('Mage_Core_Model_Layout'));
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from_date', 'to_date') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}

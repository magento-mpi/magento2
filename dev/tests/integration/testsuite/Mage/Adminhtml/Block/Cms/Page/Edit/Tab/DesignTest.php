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
 * Test class for Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_DesignTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        Mage::getConfig()->setCurrentAreaCode(Mage::helper('Mage_Backend_Helper_Data')->getAreaCode());
        Mage::register('cms_page', Mage::getObjectManager()->create('Mage_Cms_Model_Page'));

        $block = Mage::getObjectManager()->create('Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design');
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('custom_theme_to', 'custom_theme_from') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}

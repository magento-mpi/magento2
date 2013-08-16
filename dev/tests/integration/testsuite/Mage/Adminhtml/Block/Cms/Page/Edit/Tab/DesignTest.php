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
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $objectManager->get('Mage_Core_Model_Config_Scope')
            ->setCurrentScope(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        Mage::register('cms_page', $objectManager->create('Mage_Cms_Model_Page'));

        $block = $objectManager->create('Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design');
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

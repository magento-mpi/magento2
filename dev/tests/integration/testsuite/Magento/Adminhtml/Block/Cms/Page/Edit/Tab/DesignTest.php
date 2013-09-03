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
 * Test class for Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Design
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Cms_Page_Edit_Tab_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $objectManager->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        Mage::register('cms_page', Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cms_Model_Page'));

        $block = $objectManager->create('Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Design');
        $prepareFormMethod = new ReflectionMethod(
            'Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Design', '_prepareForm');
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

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
 * Test class for Mage_Adminhtml_Block_System_Design_Edit_Tab_General
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_System_Design_Edit_Tab_GeneralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        Mage::register('design', Mage::getObjectManager()->create('Mage_Core_Model_Design'));
        $layout = Mage::getObjectManager()->create('Mage_Core_Model_Layout');
        $block = $layout->addBlock('Mage_Adminhtml_Block_System_Design_Edit_Tab_General');
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_System_Design_Edit_Tab_General', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('date_from', 'date_to') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}

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
 * Test class for Magento_Adminhtml_Block_Report_Filter_Form
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Report_Filter_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Layout');
        $block = $layout->addBlock('Magento_Adminhtml_Block_Report_Filter_Form');
        $prepareFormMethod = new ReflectionMethod(
            'Magento_Adminhtml_Block_Report_Filter_Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from', 'to') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}

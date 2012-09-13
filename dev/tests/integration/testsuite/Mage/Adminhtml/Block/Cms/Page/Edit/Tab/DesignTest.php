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
 * Test class for Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design.
 *
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_DesignTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getConfig()->setCurrentAreaCode(Mage::helper("Mage_Backend_Helper_Data")->getAreaCode());
    }

    public function testPrepareForm()
    {
        Mage::register('cms_page', Mage::getModel('Mage_Cms_Model_Page'));

        $block = new Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design;
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

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

class Mage_Adminhtml_Block_System_Variable_EditTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Adminhtml/_files/system_variable.php
     */
    public function testConstruct()
    {
        $variable = Mage::registry('current_variable');
        Mage::app()->getRequest()->setParam('variable_id', $variable->getId());
        $block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_System_Variable_Edit', 'variable');
        $this->assertArrayHasKey('variable-delete_button', $block->getLayout()->getAllBlocks());
    }
}

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
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_System_Variable_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testConstruct()
    {
        $data = array(
            'code' => 'test_variable_1',
            'name' => 'Test Variable 1',
            'html_value' => '<b>Test Variable 1 HTML Value</b>',
            'plain_value' => 'Test Variable 1 plain Value',
        );
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $variable = $objectManager->create('Magento_Core_Model_Variable')
            ->setData($data)
            ->save();

        $objectManager->get('Magento_Core_Model_Registry')->register('current_variable', $variable);
        $objectManager->get('Magento_Core_Controller_Request_Http')
            ->setParam('variable_id', $variable->getId());
        $block = $objectManager->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Adminhtml_Block_System_Variable_Edit', 'variable');
        $this->assertArrayHasKey('variable-delete_button', $block->getLayout()->getAllBlocks());
    }
}

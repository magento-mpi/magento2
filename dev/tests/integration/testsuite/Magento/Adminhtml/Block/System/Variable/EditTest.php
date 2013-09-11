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
        $variable = Mage::getModel('\Magento\Core\Model\Variable')
            ->setData($data)
            ->save();

        Mage::register('current_variable', $variable);
        Mage::app()->getRequest()->setParam('variable_id', $variable->getId());
        $block = Mage::app()->getLayout()->createBlock('\Magento\Adminhtml\Block\System\Variable\Edit', 'variable');
        $this->assertArrayHasKey('variable-delete_button', $block->getLayout()->getAllBlocks());
    }
}

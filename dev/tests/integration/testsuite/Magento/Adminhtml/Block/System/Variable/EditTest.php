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
namespace Magento\Adminhtml\Block\System\Variable;

class EditTest extends \PHPUnit_Framework_TestCase
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
        $variable = \Mage::getModel('Magento\Core\Model\Variable')
            ->setData($data)
            ->save();

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_variable', $variable);
        \Mage::app()->getRequest()->setParam('variable_id', $variable->getId());
        $block = \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\System\Variable\Edit', 'variable');
        $this->assertArrayHasKey('variable-delete_button', $block->getLayout()->getAllBlocks());
    }
}

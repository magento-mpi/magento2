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
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $variable = $objectManager->create('Magento\Core\Model\Variable')
            ->setData($data)
            ->save();

        $objectManager->get('Magento\Core\Model\Registry')->register('current_variable', $variable);
        $objectManager->get('Magento\Core\Controller\Request\Http')
            ->setParam('variable_id', $variable->getId());
        $block = $objectManager->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Adminhtml\Block\System\Variable\Edit', 'variable');
        $this->assertArrayHasKey('variable-delete_button', $block->getLayout()->getAllBlocks());
    }
}

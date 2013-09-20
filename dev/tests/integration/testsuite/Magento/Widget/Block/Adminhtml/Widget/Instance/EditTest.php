<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testConstruct()
    {
        $type = 'Magento\Catalog\Block\Product\Widget\New';
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface')
            ->setDefaultDesignTheme()
            ->getDesignTheme();

        /** @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = \Mage::getModel('Magento\Widget\Model\Widget\Instance');
        $widgetInstance
            ->setType($type)
            ->setThemeId($theme->getId())
            ->save();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_widget_instance', $widgetInstance);

        \Mage::app()->getRequest()->setParam('instance_id', $widgetInstance->getId());
        $block = \Mage::app()->getLayout()->createBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit', 'widget');
        $this->assertArrayHasKey('widget-delete_button', $block->getLayout()->getAllBlocks());
    }
}

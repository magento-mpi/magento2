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

namespace Magento\Widget\Block\Adminhtml\Widget\Instance;

/**
 * @magentoAppArea adminhtml
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testConstruct()
    {
        $type = 'Magento\Catalog\Block\Product\Widget\NewWidget';
        $code = 'catalog_product_newwidget';
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme()
            ->getDesignTheme();

        /** @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget\Instance');
        $widgetInstance
            ->setType($type)
            ->setCode($code)
            ->setThemeId($theme->getId())
            ->save();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_widget_instance', $widgetInstance);

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Controller\Request\Http')
            ->setParam('instance_id', $widgetInstance->getId());
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit', 'widget');
        $this->assertArrayHasKey('widget-delete_button', $block->getLayout()->getAllBlocks());
    }
}

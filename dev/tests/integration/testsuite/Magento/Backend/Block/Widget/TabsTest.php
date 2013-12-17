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

namespace Magento\Backend\Block\Widget;

/**
 * @magentoAppArea adminhtml
 */
class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddTab()
    {
        /** @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget\Instance');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_widget_instance', $widgetInstance);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Backend\Block\Widget\Tabs */
        $block = $layout->createBlock('Magento\Backend\Block\Widget\Tabs', 'block');
        $layout->addBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main', 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(array('tab_id'), $block->getTabsIds());
    }
}

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
namespace Magento\Adminhtml\Block\Widget;

class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddTab()
    {
        /** @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = \Mage::getModel('Magento\Widget\Model\Widget\Instance');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_widget_instance', $widgetInstance);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Widget\Tabs */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Widget\Tabs', 'block');
        $layout->addBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main', 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(array('tab_id'), $block->getTabsIds());
    }
}

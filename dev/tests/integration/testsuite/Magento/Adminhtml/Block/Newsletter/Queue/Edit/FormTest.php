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
 * Test class for \Magento\Adminhtml\Block\Newsletter\Queue\Edit\Form
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Newsletter_Queue_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Adminhtml\Block\Newsletter\Queue\Edit\Form');
        $prepareFormMethod = new ReflectionMethod(
            '\Magento\Adminhtml\Block\Newsletter\Queue\Edit\Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);

        $queue = Mage::getSingleton('Magento\Newsletter\Model\Queue');
        $statuses = array(\Magento\Newsletter\Model\Queue::STATUS_NEVER, \Magento\Newsletter\Model\Queue::STATUS_PAUSE);
        foreach ($statuses as $status) {
            $queue->setQueueStatus($status);
            $prepareFormMethod->invoke($block);
            $element = $block->getForm()->getElement('date');
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getTimeFormat());
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}

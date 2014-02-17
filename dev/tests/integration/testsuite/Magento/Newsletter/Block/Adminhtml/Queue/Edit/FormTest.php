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

namespace Magento\Newsletter\Block\Adminhtml\Queue\Edit;

/**
 * Test class for \Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $queue = $objectManager->get('Magento\Newsletter\Model\Queue');
        /** @var \Magento\Registry $registry */
        $registry = $objectManager->get('\Magento\Registry');
        $registry->register('current_queue', $queue);

        $objectManager->get('Magento\View\DesignInterface')
            ->setArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
            ->setDefaultDesignTheme();
        $objectManager
            ->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $block = $objectManager
            ->create('Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form', array(
                'registry' => $registry,
            ));
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);

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

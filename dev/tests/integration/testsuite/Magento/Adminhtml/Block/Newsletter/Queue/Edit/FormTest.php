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
 * Test class for Magento_Adminhtml_Block_Newsletter_Queue_Edit_Form
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Newsletter_Queue_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        Mage::getConfig()->setCurrentAreaCode(Mage::helper('Mage_Backend_Helper_Data')->getAreaCode());
        $block = Mage::getObjectManager()->create('Magento_Adminhtml_Block_Newsletter_Queue_Edit_Form');
        $prepareFormMethod = new ReflectionMethod(
            'Magento_Adminhtml_Block_Newsletter_Queue_Edit_Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);

        $queue = Mage::getSingleton('Mage_Newsletter_Model_Queue');
        $statuses = array(Mage_Newsletter_Model_Queue::STATUS_NEVER, Mage_Newsletter_Model_Queue::STATUS_PAUSE);
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

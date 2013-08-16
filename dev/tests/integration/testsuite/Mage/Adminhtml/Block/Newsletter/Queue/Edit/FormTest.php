<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $objectManager->get('Mage_Core_Model_Config_Scope')
            ->setCurrentScope(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $block = $objectManager->create('Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form');
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form', '_prepareForm'
        );
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

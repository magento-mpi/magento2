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
class Magento_Adminhtml_Block_Widget_Form_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testGetFormHtml()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        // Create block with blocking _prepateLayout(), which is used by block to instantly add 'form' child
        /** @var $block Magento_Adminhtml_Block_Widget_Form_Container */
        $block = $this->getMock('Magento_Adminhtml_Block_Widget_Form_Container', array('_prepareLayout'),
            array(
                Mage::getObjectManager()->create('Magento_Core_Helper_Data'),
                Mage::getObjectManager()->create('Magento_Backend_Block_Template_Context'),
            )
        );

        $layout->addBlock($block, 'block');
        $form = $layout->addBlock('Magento_Core_Block_Text', 'form', 'block');

        $expectedHtml = '<b>html</b>';
        $this->assertNotEquals($expectedHtml, $block->getFormHtml());
        $form->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getFormHtml());
    }
}

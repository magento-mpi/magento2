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
class Magento_Adminhtml_Block_Widget_Grid_Massaction_ItemTest extends PHPUnit_Framework_TestCase
{
    public function testGetAdditionalActionBlock()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Widget_Grid_Massaction_Item */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Widget_Grid_Massaction_Item', 'block');
        $expected = $layout->addBlock('Magento_Core_Block_Template', 'additional_action', 'block');
        $this->assertSame($expected, $block->getAdditionalActionBlock());
    }
}

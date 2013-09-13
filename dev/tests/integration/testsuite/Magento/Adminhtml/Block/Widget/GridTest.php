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
class Magento_Adminhtml_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    public function testGetMassactionBlock()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Widget_Grid */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Widget_Grid', 'block');
        $child = $layout->addBlock('Magento_Core_Block_Template', 'massaction', 'block');
        $this->assertSame($child, $block->getMassactionBlock());
    }
}

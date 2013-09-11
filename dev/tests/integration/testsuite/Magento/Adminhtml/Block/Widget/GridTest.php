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
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel('\Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Widget\Grid */
        $block = $layout->createBlock('\Magento\Adminhtml\Block\Widget\Grid', 'block');
        $child = $layout->addBlock('\Magento\Core\Block\Template', 'massaction', 'block');
        $this->assertSame($child, $block->getMassactionBlock());
    }
}

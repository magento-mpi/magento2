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
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Widget\Grid\Massaction\Item */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Widget\Grid\Massaction\Item', 'block');
        $expected = $layout->addBlock('Magento\Core\Block\Template', 'additional_action', 'block');
        $this->assertSame($expected, $block->getAdditionalActionBlock());
    }
}

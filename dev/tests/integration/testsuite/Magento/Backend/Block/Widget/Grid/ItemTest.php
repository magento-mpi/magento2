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

namespace Magento\Backend\Block\Widget\Grid;

/**
 * @magentoAppArea adminhtml
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdditionalActionBlock()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Backend\Block\Widget\Grid\Massaction\Item */
        $block = $layout->createBlock('Magento\Backend\Block\Widget\Grid\Massaction\Item', 'block');
        $expected = $layout->addBlock('Magento\View\Element\Template', 'additional_action', 'block');
        $this->assertSame($expected, $block->getAdditionalActionBlock());
    }
}

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

namespace Magento\Adminhtml\Block\Widget;

/**
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMassactionBlock()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Adminhtml\Block\Widget\Grid */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Widget\Grid', 'block');
        $child = $layout->addBlock('Magento\View\Element\Template', 'massaction', 'block');
        $this->assertSame($child, $block->getMassactionBlock());
    }
}

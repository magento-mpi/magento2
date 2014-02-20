<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Block\Adminhtml\Dhl;

/**
 * @magentoAppArea adminhtml
 */
class UnitofmeasureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Usa\Block\Adminhtml\Dhl\Unitofmeasure */
        $block = $layout->createBlock('Magento\Usa\Block\Adminhtml\Dhl\Unitofmeasure');
        $this->assertNotEmpty($block->toHtml());
    }
}

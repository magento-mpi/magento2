<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Usa\Block\Adminhtml\Dhl;

class UnitofmeasureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(array(
            'Magento\Core\Model\Layout' => array(
                'parameters' => array('area' => 'adminhtml')
            )
        ));
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Usa\Block\Adminhtml\Dhl\Unitofmeasure */
        $block = $layout->createBlock('Magento\Usa\Block\Adminhtml\Dhl\Unitofmeasure');
        $this->assertNotEmpty($block->toHtml());
    }
}

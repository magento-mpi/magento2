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
class Magento_Usa_Block_Adminhtml_Dhl_UnitofmeasureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->configure(array(
            '\Magento\Core\Model\Layout' => array(
                'parameters' => array('area' => 'adminhtml')
            )
        ));
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Usa\Block\Adminhtml\Dhl\Unitofmeasure */
        $block = $layout->createBlock('\Magento\Usa\Block\Adminhtml\Dhl\Unitofmeasure');
        $this->assertNotEmpty($block->toHtml());
    }
}

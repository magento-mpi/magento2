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
        Mage::getObjectManager()->configure(array(
            'Magento_Core_Model_Layout' => array(
                'parameters' => array('area' => 'adminhtml')
            )
        ));
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Usa_Block_Adminhtml_Dhl_Unitofmeasure */
        $block = $layout->createBlock('Magento_Usa_Block_Adminhtml_Dhl_Unitofmeasure');
        $this->assertNotEmpty($block->toHtml());
    }
}

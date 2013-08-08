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
class Magento_Adminhtml_Block_Customer_OnlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilterFormHtml()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel(
            'Magento_Core_Model_Layout',
            array('area' => Magento_Core_Model_App_Area::AREA_ADMINHTML)
        );
        /** @var $block Magento_Adminhtml_Block_Customer_Online */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Customer_Online', 'block');
        $this->assertNotEmpty($block->getFilterFormHtml());
    }
}

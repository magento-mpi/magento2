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
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel(
            '\Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );
        /** @var $block \Magento\Adminhtml\Block\Customer\Online */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Customer\Online', 'block');
        $this->assertNotEmpty($block->getFilterFormHtml());
    }
}

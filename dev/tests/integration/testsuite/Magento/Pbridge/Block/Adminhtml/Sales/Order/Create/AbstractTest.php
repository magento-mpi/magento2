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
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate',
            Mage::app()->getLayout()->createBlock('Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate')
        );
    }
}

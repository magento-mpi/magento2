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
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract',
            Mage::app()->getLayout()->createBlock('Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract')
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rss_Block_Order_StatusTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $block = Mage::app()->getLayout()->createBlock('Magento_Rss_Block_Order_Status');
        $this->assertEmpty($block->toHtml());

        $uniqid = uniqid();
        $order = $this->getMock('Magento\Object', array('formatPrice'), array(array('id' => $uniqid,)));
        Mage::register('current_order', $order);
        $this->assertContains($uniqid, $block->toHtml());
    }
}

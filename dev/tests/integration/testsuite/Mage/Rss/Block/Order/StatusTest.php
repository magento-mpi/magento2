<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Rss_Block_Order_StatusTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        if (Magento_Test_Helper_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('bug: MAGETWO-4227');
        }
        $block = Mage::app()->getLayout()->createBlock('Mage_Rss_Block_Order_Status');
        $this->assertEmpty($block->toHtml());

        $uniqid = uniqid();
        $order = $this->getMock('Magento_Object', array('formatPrice'), array(array('id' => $uniqid,)));
        Mage::register('current_order', $order);
        $this->assertContains($uniqid, $block->toHtml());
    }
}

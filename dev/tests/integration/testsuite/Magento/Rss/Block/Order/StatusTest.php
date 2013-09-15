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
        $block = Mage::app()->getLayout()->createBlock('Magento\Rss\Block\Order\Status');
        $this->assertEmpty($block->toHtml());

        $uniqid = uniqid();
        $order = $this->getMock('Magento\Object', array('formatPrice'), array(array('id' => $uniqid,)));
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_order', $order);
        $this->assertContains($uniqid, $block->toHtml());
    }
}

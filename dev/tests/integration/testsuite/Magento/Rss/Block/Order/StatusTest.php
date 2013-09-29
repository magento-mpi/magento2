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

namespace Magento\Rss\Block\Order;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Rss\Block\Order\Status');
        $this->assertEmpty($block->toHtml());

        $uniqid = uniqid();
        $order = $this->getMock('Magento\Object', array('formatPrice'), array(array('id' => $uniqid,)));
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_order', $order);
        $this->assertContains($uniqid, $block->toHtml());
    }
}

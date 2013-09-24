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

namespace Magento\Adminhtml\Block\Newsletter;

/**
 * @magentoAppArea adminhtml
 */
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetShowQueueAdd()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Newsletter\Subscriber */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Newsletter\Subscriber', 'block');
        /** @var $childBlock \Magento\Core\Block\Template */
        $childBlock = $layout->addBlock('Magento\Core\Block\Template', 'grid', 'block');

        $expected = 'test_data';
        $this->assertNotEquals($expected, $block->getShowQueueAdd());
        $childBlock->setShowQueueAdd($expected);
        $this->assertEquals($expected, $block->getShowQueueAdd());
    }
}

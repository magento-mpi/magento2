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
class Magento_Adminhtml_Block_Newsletter_SubscriberTest extends PHPUnit_Framework_TestCase
{
    public function testGetShowQueueAdd()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Newsletter_Subscriber */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Newsletter_Subscriber', 'block');
        /** @var $childBlock Magento_Core_Block_Template */
        $childBlock = $layout->addBlock('Magento_Core_Block_Template', 'grid', 'block');

        $expected = 'test_data';
        $this->assertNotEquals($expected, $block->getShowQueueAdd());
        $childBlock->setShowQueueAdd($expected);
        $this->assertEquals($expected, $block->getShowQueueAdd());
    }
}

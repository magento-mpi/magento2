<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

/**
 * @magentoAppArea adminhtml
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate',
            \Mage::app()->getLayout()->createBlock('Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate')
        );
    }
}

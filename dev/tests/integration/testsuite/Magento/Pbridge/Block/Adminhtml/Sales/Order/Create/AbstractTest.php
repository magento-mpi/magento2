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
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

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

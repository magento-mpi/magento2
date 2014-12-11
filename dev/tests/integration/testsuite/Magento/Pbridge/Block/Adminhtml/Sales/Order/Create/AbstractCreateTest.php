<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

/**
 * @magentoAppArea adminhtml
 */
class AbstractCreateTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\View\LayoutInterface'
            )->createBlock(
                'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate'
            )
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetCustomerName()
    {
        /** Preconditions */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $fixtureCustomerId = 1;
        /** @var \Magento\Backend\Model\Session\Quote $backendQuoteSession */
        $backendQuoteSession = $objectManager->get('Magento\Backend\Model\Session\Quote');
        $backendQuoteSession->setCustomerId($fixtureCustomerId);
        /** @var \Magento\Pbridge\Block\Adminhtml\Customer\Edit\Tab\Payment\Profile $block */
        $block = $objectManager->create('Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate');

        /** SUT execution and assertions */
        $this->assertEquals('John Smith', $block->getCustomerName(), 'Customer name is invalid.');
    }
}

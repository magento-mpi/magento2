<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Observer\Frontend\Quote\Address;

class CollectTotalsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Observer\Frontend\Quote\Address\CollectTotals
     */
    protected $model;

    protected function setUp()
    {
        $this->model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('\Magento\Sales\Model\Observer\Frontend\Quote\Address\CollectTotals');
    }

    /**
     * @magentoConfigFixture current_store customer/create_account/auto_group_assign 1
     * @magentoConfigFixture current_store customer/create_account/default_group 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     *
     * @covers \Magento\Sales\Model\Observer\Frontend\Quote\Address\CollectTotals::dispatch
     */
    public function testChangeQuoteCustomerGroupIdForCustomerWithDisabledAutomaticGroupChange()
    {
        /** @var \Magento\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        $customer->setDisableAutoGroupChange(1);
        $customer->setGroupId(2);
        $customer->save();

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        $quote->setCustomer($customer);

        $quoteAddress = $quote->getBillingAddress();

        $eventObserver = $objectManager->create('Magento\Event\Observer',
            array('data' => array('quote_address' => $quoteAddress))
        );
        $this->model->dispatch($eventObserver);

        $this->assertEquals(2, $quote->getCustomer()->getGroupId());
    }

    /**
     * @magentoConfigFixture current_store customer/create_account/auto_group_assign 1
     * @magentoConfigFixture current_store customer/create_account/default_group 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     *
     * @covers \Magento\Sales\Model\Observer\Frontend\Quote\Address\CollectTotals::dispatch
     */
    public function testChangeQuoteCustomerGroupIdForCustomerWithEnabledAutomaticGroupChange()
    {
        /** @var \Magento\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        $customer->setDisableAutoGroupChange(0);
        $customer->setGroupId(2);
        $customer->save();

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        $quote->setCustomer($customer);

        $quoteAddress = $quote->getBillingAddress();

        $eventObserver = $objectManager->create('Magento\Event\Observer',
            array('data' => array('quote_address' => $quoteAddress))
        );
        $this->model->dispatch($eventObserver);

        $this->assertEquals(1, $quote->getCustomer()->getGroupId());
    }
}
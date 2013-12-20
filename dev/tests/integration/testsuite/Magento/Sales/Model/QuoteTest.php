<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_virtual.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testCollectTotalsWithVirtual()
    {
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(21);
        $quote->addProduct($product);
        $quote->collectTotals();

        $this->assertEquals(2, $quote->getItemsQty());
        $this->assertEquals(1, $quote->getVirtualItemsQty());
        $this->assertEquals(20, $quote->getGrandTotal());
        $this->assertEquals(20, $quote->getBaseGrandTotal());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testAssignCustomerWithAddressChange()
    {
        /** @var Quote $quote */
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        $quote->setIsMultiShipping('0');

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        $customer->setDefaultShipping(2);
        $customerBilling = $customer->getDefaultBillingAddress();
        $customerShipping = $customer->getDefaultShippingAddress();


        // case 1: use customer billing and shipping => shipping must not be as billing
        $quote->getShippingAddress()->setSameAsBilling('some_value');
        $quote->assignCustomerWithAddressChange($customer);
        $quoteBilling = $quote->getBillingAddress();
        $quoteShipping = $quote->getShippingAddress();
        $this->assertEquals($customerBilling->getId(), $quoteBilling->getCustomerAddressId());
        $this->assertEquals($customerShipping->getId(), $quoteShipping->getCustomerAddressId());
        $this->assertFalse($quoteShipping->getSameAsBilling());


        // case 2: pass quote billing and quote shipping to method => shipping same_as_billing must not be changed
        $billing = clone $quoteBilling;
        $billing->setStreet('billing street');
        $shipping = clone $quoteShipping;
        $shipping->setStreet('shipping street');
        $shipping->setSameAsBilling('some_value');
        $quote->assignCustomerWithAddressChange($customer, $billing, $shipping);
        $quoteBilling = $quote->getBillingAddress();
        $quoteShipping = $quote->getShippingAddress();
        $this->assertEquals($quoteBilling->getId(), $billing->getId());
        $this->assertEquals($quoteBilling->getStreet(), $billing->getStreet());
        $this->assertEquals($quoteShipping->getId(), $shipping->getId());
        $this->assertEquals($quoteShipping->getStreet(), $shipping->getStreet());
        $this->assertEquals('some_value', $quoteShipping->getSameAsBilling());

        // case 3: customer shipping equals customer billing => shipping same_as_billing must not be changed
        $customer->getDefaultShippingAddress()->setId(
            $customer->getDefaultBillingAddress()->getId()
        );
        $quote->getShippingAddress()->setSameAsBilling('some_value');
        $quote->assignCustomerWithAddressChange($customer);
        $quoteBilling = $quote->getBillingAddress();
        $quoteShipping = $quote->getShippingAddress();
        $this->assertEquals($customerBilling->getId(), $quoteBilling->getCustomerAddressId());
        $this->assertEquals($customerShipping->getId(), $quoteShipping->getCustomerAddressId());
        $this->assertEquals('some_value', $quoteShipping->getSameAsBilling());
    }
}

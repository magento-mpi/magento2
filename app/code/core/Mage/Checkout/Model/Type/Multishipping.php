<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout model
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    /**
     * Initialize multishipping checkout.
     * Split virtual/not virtual items between default billing/shipping addresses
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _init()
    {
        /**
         * reset quote shipping addresses and items
         */
        $quote = $this->getQuote();
        $quote->setIsMultiShipping(true);
        if (!$this->getCustomer()->getId()) {
            return $this;
        }

        if ($this->getCheckoutSession()->getCheckoutState() === Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN) {
            $this->getCheckoutSession()->setCheckoutState(true);
            /**
             * Remove all addresses
             */
            $addresses  = $quote->getAllAddresses();
            foreach ($addresses as $address) {
                $quote->removeAddress($address->getId());
            }

            if ($defaultShipping = $this->getCustomerDefaultShippingAddress()) {
                $quote->getShippingAddress()->importCustomerAddress($defaultShipping);

                foreach ($this->getQuoteItems() as $item) {
                    /**
                     * Items with parent id we add in importQuoteItem method.
                     * Skip virtual items
                     */
                    if ($item->getParentItemId() || $item->getProduct()->getIsVirtual()) {
                        continue;
                    }
                    $quote->getShippingAddress()->addItem($item);
                }
            }

            if ($this->getCustomerDefaultBillingAddress()) {
                $quote->getBillingAddress()
                    ->importCustomerAddress($this->getCustomerDefaultBillingAddress());
                foreach ($this->getQuoteItems() as $item) {
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if ($item->getProduct()->getIsVirtual()) {
                        $quote->getBillingAddress()->addItem($item);
                    }
                }
            }
            $this->save();
        }
        return $this;
    }

    /**
     * Get quote items assigned to different quote addresses populated per item qty.
     * Based on result array we can display each item separately
     *
     * @return array
     */
    public function getQuoteShippingAddressesItems()
    {
        $items = array();
        $addresses  = $this->getQuote()->getAllAddresses();
        foreach ($addresses as $address) {
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getProduct()->getIsVirtual()) {
                    $items[] = $item;
                    continue;
                }
                if ($item->getQty() > 1) {
                    for ($i = 0, $n = $item->getQty(); $i < $n; $i++) {
                        if ($i == 0) {
                            $addressItem = $item;
                        } else {
                            $addressItem = clone $item;
                        }
                        $addressItem->setQty(1)
                            ->setCustomerAddressId($address->getCustomerAddressId())
                            ->save();
                        $items[] = $addressItem;
                    }
                } else {
                    $item->setCustomerAddressId($address->getCustomerAddressId());
                    $items[] = $item;
                }
            }
        }
        return $items;
    }

    /**
     * Remove item from address
     *
     * @param int $addressId
     * @param int $itemId
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function removeAddressItem($addressId, $itemId)
    {
        $address = $this->getQuote()->getAddressById($addressId);
        /* @var $address Mage_Sales_Model_Quote_Address */
        if ($address) {
            $item = $address->getValidItemById($itemId);
            if ($item) {
                if ($item->getQty()>1 && !$item->getProduct()->getIsVirtual()) {
                    $item->setQty($item->getQty()-1);
                } else {
                    $address->removeItem($item->getId());
                }

                if (count($address->getAllItems()) == 0) {
                    $address->isDeleted(true);
                }

                if ($quoteItem = $this->getQuote()->getItemById($item->getQuoteItemId())) {
                    $newItemQty = $quoteItem->getQty()-1;
                    if ($newItemQty > 0 && !$item->getProduct()->getIsVirtual()) {
                        $quoteItem->setQty($quoteItem->getQty()-1);
                    } else {
                        $this->getQuote()->removeItem($quoteItem->getId());
                    }
                }
                $this->save();
            }
        }
        return $this;
    }

    /**
     * Assign quote items to addresses and specify items qty
     *
     * array structure:
     * array(
     *      $quoteItemId => array(
     *          'qty'       => $qty,
     *          'address'   => $customerAddressId
     *      )
     * )
     *
     * @param array $info
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function setShippingItemsInformation($info)
    {
        if (is_array($info)) {
            $allQty = 0;
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $allQty += $data['qty'];
                }
            }

            $maxQty = (int)Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty');
            if ($allQty > $maxQty) {
                Mage::throwException(Mage::helper('checkout')->__('Maximum qty allowed for Shipping to multiple addresses is %s', $maxQty));
            }
            $quote = $this->getQuote();
            $addresses  = $quote->getAllShippingAddresses();
            foreach ($addresses as $address) {
                $quote->removeAddress($address->getId());
            }

            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $this->_addShippingItem($quoteItemId, $data);
                }
            }

            /**
             * Delete all not virtual quote items which are not added to shipping address
             * MultisippingQty should be defined for each quote item when it processed with _addShippingItem
             */
            foreach ($quote->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual() && !$_item->getMultisippingQty()) {
                    $_item->delete();
                }
            }

            if ($billingAddress = $quote->getBillingAddress()) {
                $quote->removeAddress($billingAddress->getId());
            }

            $quote->getBillingAddress()->importCustomerAddress($this->getCustomerDefaultBillingAddress());

            foreach ($quote->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual()) {
                    continue;
                }
                if (isset($itemData[$_item->getId()]['qty']) && ($qty = (int)$itemData[$_item->getId()]['qty'])) {
                    $_item->setQty($qty);
                }
                $quote->getBillingAddress()->addItem($_item);
            }

            $this->save();
            Mage::dispatchEvent('checkout_type_multishipping_set_shipping_items', array('quote'=>$quote));
        }
        return $this;
    }

    /**
     * Add quote item to specific shipping address based on customer address id
     *
     * @param int $quoteItemId
     * @param array $data array('qty'=>$qty, 'address'=>$customerAddressId)
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _addShippingItem($quoteItemId, $data)
    {
        $qty       = isset($data['qty']) ? (int) $data['qty'] : 1;
        //$qty       = $qty > 0 ? $qty : 1;
        $addressId = isset($data['address']) ? (int) $data['address'] : false;
        $quoteItem = $this->getQuote()->getItemById($quoteItemId);

        if ($addressId && $quoteItem) {
            /**
             * Decrease quote item QTY if address item has QTY 0 and skip this item processing
             */
            if ($qty === 0) {
                $quoteItemQty = $quoteItem->getQty();
                if ($quoteItemQty > 0) {
                    $quoteItem->setQty($quoteItemQty-1);
                }
                return $this;
            }
            $quoteItem->setMultisippingQty((int)$quoteItem->getMultisippingQty()+$qty);
            $quoteItem->setQty($quoteItem->getMultisippingQty());
            $address = $this->getCustomer()->getAddressById($addressId);
            if ($address->getId()) {
                if (!$quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($addressId)) {
                    $quoteAddress = Mage::getModel('sales/quote_address')->importCustomerAddress($address);
                    $this->getQuote()->addShippingAddress($quoteAddress);
                }

                $quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($address->getId());
                if ($quoteAddressItem = $quoteAddress->getItemByQuoteItemId($quoteItemId)) {
                    $quoteAddressItem->setQty((int)($quoteAddressItem->getQty()+$qty));
                } else {
                    $quoteAddress->addItem($quoteItem, $qty);
                }
                /**
                 * Require shiping rate recollect
                 */
                $quoteAddress->setCollectShippingRates((boolean) $this->getCollectRatesFlag());
            }
        }
        return $this;
    }

    /**
     * Reimport customer address info to quote shipping address
     *
     * @param int $addressId customer address id
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function updateQuoteCustomerShippingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $this->getQuote()->getShippingAddressByCustomerAddressId($addressId)
                ->setCollectShippingRates(true)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->save();
        }
        return $this;
    }

    /**
     * Reimport customer billing address to quote
     *
     * @param int $addressId customer address id
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function setQuoteCustomerBillingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $this->getQuote()->getBillingAddress($addressId)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->collectTotals()->save();
        }
        return $this;
    }

    /**
     * Assign shipping methods to addresses
     *
     * @param  array $methods
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function setShippingMethods($methods)
    {
        $addresses = $this->getQuote()->getAllShippingAddresses();
        foreach ($addresses as $address) {
            if (isset($methods[$address->getId()])) {
                $address->setShippingMethod($methods[$address->getId()]);
            } elseif (!$address->getShippingMethod()) {
                Mage::throwException(Mage::helper('checkout')->__('Please select shipping methods for all addresses'));
            }
        }
        $this->save();
        return $this;
    }

    /**
     * Set payment method info to quote payment
     *
     * @param array $payment
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function setPaymentMethod($payment)
    {
        if (!isset($payment['method'])) {
            Mage::throwException(Mage::helper('checkout')->__('Payment method is not defined'));
        }
        $this->getQuote()->getPayment()
            ->importData($payment);
        $this->getQuote()->save();
        return $this;
    }

    /**
     * Prepare order based on quote address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Order
     */
    protected function _prepareOrder(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $this->getQuote();
        $quote->unsReservedOrderId();
        $quote->reserveOrderId();
        $convertQuote = Mage::getSingleton('sales/convert_quote');
        $order = $convertQuote->addressToOrder($address);
        $order->setBillingAddress(
            $convertQuote->addressToOrderAddress($quote->getBillingAddress())
        );

        if ($address->getAddressType() == 'billing') {
            $order->setIsVirtual(1);
        } else {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($address));
        }
        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));

        foreach ($address->getAllItems() as $item) {
            $item->setProductType($item->getQuoteItem()->getProductType())
                ->setProductOptions($item->getQuoteItem()->getProduct()->getTypeInstance(true)->getOrderOptions($item->getQuoteItem()->getProduct()));
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        return $order;
    }

    /**
     * Validate quote data
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _validate()
    {
        $helper = Mage::helper('checkout');
        $quote = $this->getQuote();
        if (!$quote->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }

        $addresses = $quote->getAllShippingAddresses();
        foreach ($addresses as $address) {
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException($helper->__('Please check shipping addresses information.'));
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$method || !$rate) {
                Mage::throwException($helper->__('Please specify shipping methods for all addresses.'));
            }
        }
        $addressValidation = $quote->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException($helper->__('Please check billing address information.'));
        }
        return $this;
    }

    /**
     * Create orders per each quote address
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function createOrders()
    {
        $orderIds = array();
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = array();

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        foreach ($shippingAddresses as $address) {
            $order = $this->_prepareOrder($address);

            $orders[] = $order;
            Mage::dispatchEvent(
                'checkout_type_multishipping_create_orders_single',
                array('order'=>$order, 'address'=>$address)
            );
        }

        foreach ($orders as $order) {
            $order->place();
            $order->save();
            $order->sendNewOrderEmail();
            $orderIds[$order->getId()] = $order->getIncrementId();
        }

        Mage::getSingleton('core/session')->setOrderIds($orderIds);
        Mage::getSingleton('checkout/session')->setLastQuoteId($this->getQuote()->getId());

        $this->getQuote()
            ->setIsActive(false)
            ->save();
        return $this;
    }

    /**
     * Collect quote totals and save quote object
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function save()
    {
        $this->getQuote()->collectTotals()
            ->save();
        return $this;
    }

    /**
     * Specify BEGIN state in checkout session whot allow reinit multishipping checkout
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function reset()
    {
        $this->getCheckoutSession()->setCheckoutState(Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN);
        return $this;
    }

    /**
     * Check if quote amount is allowed for multishipping checkout
     *
     * @return bool
     */
    public function validateMinimumAmount()
    {
        return !(Mage::getStoreConfigFlag('sales/minimum_order/active')
            && Mage::getStoreConfigFlag('sales/minimum_order/multi_address')
            && !$this->getQuote()->validateMinimumAmount());
    }

    /**
     * Get notification message for case when multishipping checkout is not allowed
     *
     * @return string
     */
    public function getMinimumAmountDescription()
    {
        $descr = Mage::getStoreConfig('sales/minimum_order/multi_address_description');
        if (empty($descr)) {
            $descr = Mage::getStoreConfig('sales/minimum_order/description');
        }
        return $descr;
    }

    public function getMinimumAmountError()
    {
        $error = Mage::getStoreConfig('sales/minimum_order/multi_address_error_message');
        if (empty($error)) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
        }
        return $error;
    }

    /**
     * Function is deprecated. Moved into helper.
     *
     * Check if multishipping checkout is available.
     * There should be a valid quote in checkout session. If not, only the config value will be returned.
     *
     * @return bool
     */
    public function isCheckoutAvailable()
    {
        return Mage::helper('checkout')->isMultishippingCheckoutAvailable();
    }
}

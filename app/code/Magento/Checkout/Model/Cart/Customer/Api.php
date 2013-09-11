<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart api for customer data
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model\Cart\Customer;

class Api extends \Magento\Checkout\Model\Api\Resource\Customer
{
    public function __construct(\Magento\Api\Helper\Data $apiHelper)
    {
        parent::__construct($apiHelper);
        $this->_storeIdSessionField = "cart_store_id";

        $this->_attributesMap['quote'] = array('quote_id' => 'entity_id');
        $this->_attributesMap['quote_customer'] = array('customer_id' => 'entity_id');
        $this->_attributesMap['quote_address'] = array('address_id' => 'entity_id');
    }

    /**
     * Set customer for shopping cart
     *
     * @param int $quoteId
     * @param array|object $customerData
     * @param int | string $store
     * @return int
     */
    public function set($quoteId, $customerData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $customerData = $this->_prepareCustomerData($customerData);
        if (!isset($customerData['mode'])) {
            $this->_fault('customer_mode_is_unknown');
        }

        switch ($customerData['mode']) {
            case self::MODE_CUSTOMER:
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->_getCustomer($customerData['entity_id']);
                $customer->setMode(self::MODE_CUSTOMER);
                break;
            case self::MODE_REGISTER:
            case self::MODE_GUEST:
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = \Mage::getModel('Magento\Customer\Model\Customer')->setData($customerData);

                if ($customer->getMode() == self::MODE_GUEST) {
                    $password = $customer->generatePassword();
                    $customer->setPassword($password)
                        ->setConfirmation($password);
                }

                $isCustomerValid = $customer->validate();
                if ($isCustomerValid !== true && is_array($isCustomerValid)) {
                    $this->_fault('customer_data_invalid', implode(PHP_EOL, $isCustomerValid));
                }
                break;
        }

        try {
            $quote->setCustomer($customer)
                ->setCheckoutMethod($customer->getMode())
                ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
                ->collectTotals()
                ->save();
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('customer_not_set', $e->getMessage());
        }

        return true;
    }

    /**
     * @param int $quoteId
     * @param array of array|object $customerAddressData
     * @param int|string $store
     * @return int
     */
    public function setAddresses($quoteId, $customerAddressData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $customerAddressData = $this->_prepareCustomerAddressData($customerAddressData);
        if (is_null($customerAddressData)) {
            $this->_fault('customer_address_data_empty');
        }

        foreach ($customerAddressData as $addressItem) {
            /** @var $address \Magento\Sales\Model\Quote\Address */
            $address = \Mage::getModel('Magento\Sales\Model\Quote\Address');
            $addressMode = $addressItem['mode'];
            unset($addressItem['mode']);

            if (!empty($addressItem['entity_id'])) {
                $customerAddress = $this->_getCustomerAddress($addressItem['entity_id']);
                if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                    $this->_fault('address_not_belong_customer');
                }
                $address->importCustomerAddress($customerAddress);

            } else {
                $address->setData($addressItem);
            }

            if (($validateRes = $address->validate())!==true) {
                $this->_fault('customer_address_invalid', implode(PHP_EOL, $validateRes));
            }

            switch ($addressMode) {
                case self::ADDRESS_BILLING:
                    $address->setEmail($quote->getCustomer()->getEmail());

                    if (!$quote->isVirtual()) {
                        $useCase = isset($addressItem['use_for_shipping']) ? (int)$addressItem['use_for_shipping'] : 0;
                        switch ($useCase) {
                            case 0:
                                $shippingAddress = $quote->getShippingAddress();
                                $shippingAddress->setSameAsBilling(0);
                                break;
                            case 1:
                                $billingAddress = clone $address;
                                $billingAddress->unsAddressId()->unsAddressType();

                                $shippingAddress = $quote->getShippingAddress();
                                $shippingMethod = $shippingAddress->getShippingMethod();
                                $shippingAddress->addData($billingAddress->getData())
                                    ->setSameAsBilling(1)
                                    ->setShippingMethod($shippingMethod)
                                    ->setCollectShippingRates(true);
                                break;
                        }
                    }
                    $quote->setBillingAddress($address);
                    break;

                case self::ADDRESS_SHIPPING:
                    $address->setCollectShippingRates(true)
                        ->setSameAsBilling(0);
                    $quote->setShippingAddress($address);
                    break;
            }

        }

        try {
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            $this->_fault('address_is_not_set', $e->getMessage());
        }

        return true;
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param array $data
     * @return array
     */
    protected function _prepareCustomerData($data)
    {
        foreach ($this->_attributesMap['quote_customer'] as $attributeAlias => $attributeCode) {
            if (isset($data[$attributeAlias])) {
                $data[$attributeCode] = $data[$attributeAlias];
                unset($data[$attributeAlias]);
            }
        }
        return $data;
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param array $data
     * @return array
     */
    protected function _prepareCustomerAddressData($data)
    {
        if (!is_array($data) || !is_array($data[0])) {
            return null;
        }

        $dataAddresses = array();
        foreach ($data as $addressItem) {
            foreach ($this->_attributesMap['quote_address'] as $attributeAlias => $attributeCode) {
                if (isset($addressItem[$attributeAlias])) {
                    $addressItem[$attributeCode] = $addressItem[$attributeAlias];
                    unset($addressItem[$attributeAlias]);
                }
            }
            $dataAddresses[] = $addressItem;
        }
        return $dataAddresses;
    }
}

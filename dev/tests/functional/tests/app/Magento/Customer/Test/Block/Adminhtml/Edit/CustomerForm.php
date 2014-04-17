<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;

/**
 * Custom form
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Edit
 */
class CustomerForm extends FormTabs
{
    /**
     * @var string
     */
    protected $addressesTabs = '#address-tabs';

    /**
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     * @return $this
     */
    public function fillCustomer(CustomerInjectable $customer, AddressInjectable $address)
    {
        parent::fill($customer);

        $dataAddress = $address->getData();
        if (null !== $dataAddress) {
            $tabAddresses = $this->getTabElement('addresses');

            $this->openTab('addresses');
            $tabAddresses->fillFormTab(
                $dataAddress,
                $this->_rootElement->find($this->addressesTabs)
            );
        }

        return $this;
    }

    /**
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     * @return bool
     */
    public function verifyCustomer(CustomerInjectable $customer, AddressInjectable $address) {
        $isVerify = parent::verify($customer);

        $dataAddress = $address->getData();
        if (null !== $dataAddress) {
            $addressesTabs = $this->_rootElement->find($this->addressesTabs);

            $this->openTab('addresses');
            $isVerifyAddresses = $this->getTabElement('addresses')->verifyFormTab($dataAddress, $addressesTabs);

            $isVerify = $isVerify && $isVerifyAddresses;
        }

        return $isVerify;
    }
}

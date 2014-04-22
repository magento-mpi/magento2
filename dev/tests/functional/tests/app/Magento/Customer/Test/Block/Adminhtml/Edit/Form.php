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
 * Class Form
 * Form for creation of the customer
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Edit
 */
class Form extends FormTabs
{
    /**
     * Fill Customer forms on tabs by customer, addresses data
     *
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
            $tabAddresses->fillFormTab($dataAddress, $this->_rootElement);
        }

        return $this;
    }

    /**
     * Verify Customer information, addresses on tabs.
     *
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     * @return bool
     */
    public function verifyCustomer(CustomerInjectable $customer, AddressInjectable $address = null)
    {
        $isVerify = parent::verify($customer);

        $dataAddress = $address->getData();
        if (null !== $dataAddress) {
            $this->openTab('addresses');

            $isVerifyAddresses = $this->getTabElement('addresses')->verifyFormTab($dataAddress, $this->_rootElement);
            $isVerify = $isVerify && $isVerifyAddresses;
        }

        return $isVerify;
    }
}

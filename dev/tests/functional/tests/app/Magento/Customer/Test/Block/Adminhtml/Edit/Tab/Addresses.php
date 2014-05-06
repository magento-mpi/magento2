<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Addresses
 * Customer addresses edit block
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Edit\Tab
 */
class Addresses extends Tab
{
    /**
     * "Add New Customer" button
     *
     * @var string
     */
    protected $addNewAddress = '#add_address_button';

    /**
     * Open customer address
     *
     * @var string
     */
    protected $customerAddress = '//*[@id="address_list"]/li[%d]/a';

    /**
     * Fill customer addresses
     *
     * @param FixtureInterface|FixtureInterface[] $address
     * @return $this
     */
    public function fillAddresses($address)
    {
        $addresses = is_array($address) ? $address : [$address];
        foreach ($addresses as $address) {
            $this->addNewAddress();
            $this->fillFormTab($address->getData(), $this->_rootElement);
        }

        return $this;
    }

    /**
     * Update customer addresses
     *
     * @param FixtureInterface|FixtureInterface[] $address
     * @return $this
     * @throws \Exception
     */
    public function updateAddresses($address)
    {
        $addresses = is_array($address) ? $address : [1 => $address];
        foreach ($addresses as $addressNumber => $address) {
            /* Throw exception if isn't exist previous customer address. */
            if (1 < $addressNumber && !$this->isVisibleCustomerAddress($addressNumber - 1)) {
                throw new \Exception("Invalid argument: can't update customer address #{$addressNumber}");
            }

            if (!$this->isVisibleCustomerAddress($addressNumber)) {
                $this->addNewAddress();
            }
            $this->openCustomerAddress($addressNumber);
            $this->fillFormTab($address->getData(), $this->_rootElement);
        }

        return $this;
    }

    /**
     * Get data of Customer addresses
     *
     * @param FixtureInterface|FixtureInterface[]|null $address
     * @return array
     * @throws \Exception
     */
    public function getDataAddresses($address = null)
    {
        $data = [];
        $addresses = is_array($address) ? $address : [1 => $address];

        foreach ($addresses as $addressNumber => $address) {
            $isHasData = (null === $address) || $address->hasData();
            $isVisibleCustomerAddress = $this->isVisibleCustomerAddress($addressNumber);

            if ($isHasData && !$isVisibleCustomerAddress) {
                throw new \Exception("Invalid argument: can't get data from customer address #{$addressNumber}");
            }

            if (!$isHasData && !$isVisibleCustomerAddress) {
                $data[$addressNumber] = [];
            } else {
                $this->openCustomerAddress($addressNumber);
                $data[$addressNumber] = $this->getData($address, $this->_rootElement);
            }
        }

        return $data;
    }

    /**
     * Get data to fields on tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        /* Skip get data for standard method. Use getDataAddresses. */
        return [];
    }

    /**
     * Click "Add New Address" button
     */
    protected function addNewAddress()
    {
        $this->_rootElement->find($this->addNewAddress)->click();
    }

    /**
     * Open customer address
     *
     * @param int $addressNumber
     * @throws \Exception
     */
    protected function openCustomerAddress($addressNumber)
    {
        $addressTab = $this->_rootElement->find(
            sprintf($this->customerAddress, $addressNumber),
            Locator::SELECTOR_XPATH
        );

        if (!$addressTab->isVisible()) {
            throw new \Exception("Can't open customer address #{$addressNumber}");
        }
        $addressTab->click();
    }

    /**
     * Check is visible customer address
     *
     * @param int $addressNumber
     * @return bool
     */
    protected function isVisibleCustomerAddress($addressNumber)
    {
        $addressTab = $this->_rootElement->find(
            sprintf($this->customerAddress, $addressNumber),
            Locator::SELECTOR_XPATH
        );
        return $addressTab->isVisible();
    }
}

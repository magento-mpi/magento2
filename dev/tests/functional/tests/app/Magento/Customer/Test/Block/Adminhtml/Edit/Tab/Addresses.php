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
     * @param FixtureInterface|FixtureInterface[]|null $address
     * @return $this
     * @throws \Exception
     */
    public function fillAddresses($address)
    {
        if (null !== $address) {
            $addresses = is_array($address) ? $address : [$address];

            foreach ($addresses as $address) {
                $addressData = $address->getData();
                if (null !== $addressData) {
                    $this->fillFormTab($addressData, $this->_rootElement);
                }
            }
        }

        return $this;
    }

    /**
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $this->addNewAddress();
        parent::fillFormTab($fields, $element);

        return $this;
    }

    /**
     * Verify customer addresses
     *
     * @param FixtureInterface|FixtureInterface[]|null $address
     * @return bool
     */
    public function verifyAddresses($address)
    {
        $addresses = is_array($address) ? $address : [1 => $address];

        foreach ($addresses as $addressNumber => $address) {
            $addressData = $address->getData();
            if (null !== $addressData) {
                $this->openCustomerAddress($addressNumber);
                if (!$this->verifyFormTab($addressData, $this->_rootElement)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return $this
     */
    public function addNewAddress()
    {
        $this->_rootElement->find($this->addNewAddress)->click();
        return $this;
    }

    /**
     * @param int $addressNumber
     * @return $this
     * @throws \Exception
     */
    public function openCustomerAddress($addressNumber)
    {
        $addressTab = $this->_rootElement->find(
            sprintf($this->customerAddress, $addressNumber),
            Locator::SELECTOR_XPATH
        );

        if (!$addressTab->isVisible()) {
            throw new \Exception("Can't open customer address #{$addressNumber}");
        }
        $addressTab->click();

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit;

use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 * Form for creation of the customer
 *
 */
class Form extends FormTabs
{
    /**
     * Fill Customer forms on tabs by customer, addresses data
     *
     * @param FixtureInterface $customer
     * @param FixtureInterface|FixtureInterface[]|null $address
     * @return $this
     */
    public function fillCustomer(FixtureInterface $customer, $address = null)
    {
        parent::fill($customer);

        if (null !== $address) {
            $this->openTab('addresses');
            $this->getTabElement('addresses')->fillAddresses($address);
        }

        return $this;
    }

    /**
     * Verify Customer information, addresses on tabs.
     *
     * @param FixtureInterface $customer
     * @param FixtureInterface|FixtureInterface[]|null $address
     * @return bool
     */
    public function verifyCustomer(FixtureInterface $customer, $address = null)
    {
        $isVerify = parent::verify($customer);

        if (null !== $address) {
            $this->openTab('addresses');
            $isVerify = $isVerify && $this->getTabElement('addresses')->verifyAddresses($address);
        }

        return $isVerify;
    }
}

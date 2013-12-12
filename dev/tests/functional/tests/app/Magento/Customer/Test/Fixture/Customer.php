<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Customer
 *
 * @package Magento\Customer\Test\Fixture
 */
class Customer extends DataFixture
{
    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getSecondShippingAddress()
    {
        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_US_2');
        $customerAddress->setCustomer($this);
        return $customerAddress;
    }

    /**
     * Create customer via frontend
     */
    public function persist()
    {
        Factory::getApp()->magentoCustomerCreateCustomer($this);
    }

    /**
     * Get customer email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('fields/email/value');
    }

    /**
     * Get customer password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getData('fields/password/value');
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getData('fields/firstname/value');
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getData('fields/lastname/value');
    }

    /**
     * Get billing address for customer
     *
     * @return Address
     */
    public function getDefaultBillingAddress()
    {
        $defaultBilling = $this->getData('addresses/default_billing');
        if (!empty($defaultBilling)) {
            $defaultBilling->setCustomer($this);
            return $defaultBilling;
        } else {
            $defaultBilling = Factory::getFixtureFactory()->getMagentoCustomerAddress();
            $defaultBilling->switchData('address_US_1');
            $defaultBilling->setCustomer($this);
            return $defaultBilling;
        }
    }

    /**
     * Get default shipping address for customer
     *
     * @return Address
     */
    public function getDefaultShippingAddress()
    {
        $defaultShipping = $this->getData('addresses/default_billing');
        if (!empty($defaultShipping)) {
            $defaultShipping->setCustomer($this);
            return $defaultShipping;
        } else {
            $defaultShipping = Factory::getFixtureFactory()->getMagentoCustomerAddress();
            $defaultShipping->switchData('address_US_1');
            $defaultShipping->setCustomer($this);
            return $defaultShipping;
        }
    }

    /**
     * Get customer group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->getData('fields/group_id/value');
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_defaultConfig = array(
            'grid_filter' => array('email'),
            'constraint' => 'Success'
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerCustomer($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('customer_US_1');
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getAddressData()
    {
        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_data_US_1');
        $customerAddress->setCustomer($this);
        return $customerAddress;
    }
}

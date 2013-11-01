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
    public function getDefaultAddress()
    {
        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_data_US_1');
        return $customerAddress;
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getDefaultShippingAddress()
    {
        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_US_1');
        return $customerAddress;
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getSecondShippingAddress()
    {
        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_US_2');
        return $customerAddress;
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getDefaultBillingAddress()
    {
        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_US_1');
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
     * Get billing address for customer if it is set OR default billing address otherwise
     *
     * @return Address
     */
    public function getBillingAddress()
    {
        $default_billing = $this->getData('addresses/default_billing');
        if (!empty($default_billing)) {
            return $default_billing;
        } else {
            return $this->getDefaultBillingAddress();
        }
    }

    /**
     * Get shipping address for customer if it is set OR default shipping address otherwise
     *
     * @return Address
     */
    public function getShippingAddress()
    {
        $default_shipping = $this->getData('addresses/default_billing');
        if (!empty($default_shipping)) {
            return $default_shipping;
        } else {
            return $this->getDefaultShippingAddress();
        }
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_defaultConfig = array(
            'block_form_class' => '\\Magento\\Customer\\Test\Block\\Backend\\CustomerForm',
            'block_grid_class' => '\\Magento\\Customer\\Test\Block\\Backend\\CustomerGrid',

            'grid_filter' => array('email'),

            'url_create_page' => 'admin/customer/new',
            'url_update_page' => 'admin/customer/edit',
            'url_grid_page' => 'admin/customer/index',

            'constraint' => 'Success'
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerCustomer($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('customer_US_1');
    }
}

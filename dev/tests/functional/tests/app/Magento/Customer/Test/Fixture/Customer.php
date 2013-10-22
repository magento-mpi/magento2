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
        return Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_data_US_1');
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getDefaultShippingAddress()
    {
        return Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_US_1');
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getSecondShippingAddress()
    {
        return Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_US_2');
    }

    /**
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getDefaultBillingAddress()
    {
        return Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_US_1');
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
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

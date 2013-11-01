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

namespace Magento\Customer\Test\Fixture\Customer;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class SoapCustomer
 *
 * @package Magento\Customer\Test\Fixture\Customer
 */
class SoapCustomer extends DataFixture
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'email' => 'test%isolation%@test.com',
                'firstname' => 'Test Name',
                'lastname' => 'Test Lastname',
                'middlename' => 'Test Middlename',
                'password' => '123123q',
                'website_id' => '1',
                'store_id' => '1',
                'group_id' => '1',
                'prefix' => 'test_prefix',
                'suffix' => 'test_suffix',
                'dob' => '12-12-2012',
                'taxvat' => '',
                'gender' => ''
            )
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        Factory::getApp()->magentoCustomerCreateCustomer($this);
    }
}

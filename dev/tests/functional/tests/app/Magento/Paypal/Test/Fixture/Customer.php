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

namespace Magento\Paypal\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Customer
 * Paypal buyer account
 *
 * @package Magento\Paypal\Test\Fixture
 */
class Customer extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'customer_US' => array(
                'data' => array(
                    'fields' => array(
                        'login_email' => array(
                            'value' => 'mtf_buyer@example.com'
                        ),
                        'login_password' => array(
                            'value' => '12345678'
                        )
                    )
                )
            ),

            'address_US_1' => array(
                'data' => array(
                    'fields' => array(
                        'firstname' => array(
                            'value' => 'Dmytro'
                        ),
                        'lastname' => array(
                            'value' => 'Aponasenko'
                        ),
                        'street_1' => array(
                            'value' => '1 Main St'
                        ),
                        'city' => array(
                            'value' => 'San Jose'
                        ),
                        'region' => array(
                            'value' => 'California',
                            'input' => 'select'
                        ),
                        'postcode' => array(
                            'value' => '95131'
                        ),
                        'country' => array(
                            'value' => 'United States',
                            'input' => 'select'
                        )
                    )
                )
            ),
        );

        //Default data set
        $this->switchData('customer_US');
    }
}

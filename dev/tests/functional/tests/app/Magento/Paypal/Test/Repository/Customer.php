<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Customer Repository
 * Paypal buyer account
 *
 */
class Customer extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['customer_US'] = $this->_getCustomerUS();
        $this->_data['address_US_1'] = $this->_getAddressUS1();
    }

    protected function _getCustomerUS()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'login_email' => array(
                        'value' => 'mtf_personal@example.com'
                    ),
                    'login_password' => array(
                        'value' => '12345678'
                    )
                )
            )
        );
    }

    protected function _getAddressUS1()
    {
        return array(
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
                        'value' => 'Culver City'
                    ),
                    'region_id' => array(
                        'value' => 'California',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '90230'
                    ),
                    'country_id' => array(
                        'value' => 'United States',
                        'input' => 'select'
                    )
                )
            )
        );
    }
}

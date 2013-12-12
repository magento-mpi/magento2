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

namespace Magento\Customer\Test\Repository;

use Mtf\Factory\Factory;
use Mtf\Repository\AbstractRepository;

/**
 * Class Customer Repository
 *
 * @package Magento\Customer\Test\Fixture
 */
class Customer extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['customer_US_1'] = $this->_getUS1();
        $this->_data['customer_DE'] = $this->_getGermanyCustomer();
        $this->_data['customer_UK'] = $this->_getUnitedKingdomCustomer();
        $this->_data['backend_customer'] = $this->_getBackendCustomer();
    }

    protected function _getUS1()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'John',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'lastname' => array(
                        'value' => 'Doe',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'email' => array(
                        'value' => 'John.Doe%isolation%@example.com',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'password' => array(
                        'value' => '123123q'
                    ),
                    'confirmation' => array(
                        'value' => '123123q'
                    )
                ),
            )
        );
    }

    protected function _getGermanyCustomer()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'Jan',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'lastname' => array(
                        'value' => 'Jansen',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'email' => array(
                        'value' => 'Jan.Jansen%isolation%@example.com',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'password' => array(
                        'value' => '123123q'
                    ),
                    'confirmation' => array(
                        'value' => '123123q'
                    )
                ),
            )
        );
    }

    protected function _getUnitedKingdomCustomer()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'Jane',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'lastname' => array(
                        'value' => 'Doe',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'email' => array(
                        'value' => 'Jane.Doe%isolation%@example.com',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'password' => array(
                        'value' => '123123q'
                    ),
                    'confirmation' => array(
                        'value' => '123123q'
                    )
                ),
            )
        );
    }

    protected function _getBackendCustomer()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'John',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'lastname' => array(
                        'value' => 'Doe',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'email' => array(
                        'value' => 'John.Doe%isolation%@example.com',
                        'group' => 'customer_info_tabs_account'
                    ),
                    'website_id' => array(
                        'value' => 'Main Website',
                        'group' => 'customer_info_tabs_account',
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'group_id' => array(
                        'value' => 'General',
                        'group' => 'customer_info_tabs_account',
                        'input' => 'select',
                        'input_value' => '1'
                    )
                ),
                'addresses' => array()
            )
        );
    }
}

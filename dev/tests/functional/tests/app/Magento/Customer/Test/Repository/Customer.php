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
        $this->_data['backend_customer'] = $this->_getBackendCustomer(array('value' => 'General', 'input_value' => '1'));
        $this->_data['backend_retailer_customer'] = $this->_getBackendCustomer(array('value' => 'Retailer', 'input_value' => '3'));
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

    protected function _getBackendCustomer($groupType)
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
                        'value' => $groupType['value'],
                        'group' => 'customer_info_tabs_account',
                        'input' => 'select',
                        'input_value' => $groupType['input_value']
                    )
                ),
                'addresses' => array()
            )
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CustomerInjectable
 * Customer repository
 */
class CustomerInjectable extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'group_id' => ['dataSet' => 'General'],
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
        ];

        $this->_data['johndoe'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'dob' => '01/01/1990',
            'gender' => 'Male',
            'group_id' => ['dataSet' => 'General'],
        ];

        $this->_data['johndoe_retailer'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'group_id' => ['dataSet' => 'Retailer'],
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'dob' => '01/01/1990',
            'gender' => 'Male',
        ];

        $this->_data['johndoe_with_balance'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'dob' => '01/01/1990',
            'gender' => 'Male',
            'amount_delta' => 501
        ];

        $this->_data['defaultBackend'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
        ];

        $this->_data['johndoe_with_addresses'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'group_id' => 'General',
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'address' => ['presets' => 'US_address']
        ];
    }
}

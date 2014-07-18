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
 *
 * @package Magento\Customer\Test\Repository
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
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
        ];

        $this->_data['defaultBackend'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
        ];

        $this->_data['customer_reward_points'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'reward_points_delta' => 50,
            'address' => ['dataSet' => 'customer_US']
        ];

        $this->_data['customer_store_credit'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'store_credit' => 5,
            'address' => ['dataSet' => 'customer_US']
        ];

        $this->_data['customer_US'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'address' => ['dataSet' => 'customer_US']
        ];

        $this->_data['customer_UK'] = [
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'JaneDoe_%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'address' => ['dataSet' => 'customer_UK']
        ];
    }
}

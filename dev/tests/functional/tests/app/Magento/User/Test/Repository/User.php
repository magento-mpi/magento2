<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Repository;

use Mtf\Repository\AbstractRepository;
use Mtf\ObjectManager;

/**
 * Class User
 * User Repository
 */
class User extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        /** @var \Mtf\System\Config $systemConfig */
        $systemConfig = ObjectManager::getInstance()->create('Mtf\System\Config');
        $superAdminPassword = $systemConfig->getConfigParam('application/backend_user_credentials/password');
        $this->_data['default'] = [
            'username' => 'admin',
            'firstname' => 'FirstName%isolation%',
            'lastname' => 'LastName%isolation%',
            'email' => 'email%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'user_id' => 1,
            'current_password' => $superAdminPassword
        ];

        $this->_data['custom_admin'] = [
            'username' => 'AdminUser%isolation%',
            'firstname' => 'FirstName%isolation%',
            'lastname' => 'LastName%isolation%',
            'email' => 'email%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'current_password' => $superAdminPassword
        ];

        $this->_data['custom_admin_with_default_role'] = [
            'username' => 'AdminUser%isolation%',
            'firstname' => 'FirstName%isolation%',
            'lastname' => 'LastName%isolation%',
            'email' => 'email%isolation%@example.com',
            'password' => '123123q',
            'password_confirmation' => '123123q',
            'role_id' => ['dataSet' => 'default'],
            'current_password' => $superAdminPassword
        ];
    }
}

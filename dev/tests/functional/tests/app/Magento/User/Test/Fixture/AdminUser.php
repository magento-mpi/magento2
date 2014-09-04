<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Fixture;

use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;
use Mtf\System\Config;
use Mtf\ObjectManager;

/**
 * Fixture with all necessary data for user creation on backend
 *
 */
class AdminUser extends DataFixture
{
    /**
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        $placeholders['password'] = isset($placeholders['password']) ? $placeholders['password'] : '123123q';
        parent::__construct($configuration, $placeholders);
        $this->_placeholders['sales_all_scopes'] = array($this, 'roleProvider');
    }

    /**
     * Retrieve specify data from role.trieve specify data from role.
     *
     * @param $roleName
     * @return mixed
     */
    protected function roleProvider($roleName)
    {
        $role = Factory::getFixtureFactory()->getMagentoUserRole();
        $role->switchData($roleName);
        $data = $role->persist();
        return $data['id'];
    }

    /**
     * initialize data
     */
    protected function _initData()
    {
        /** @var \Mtf\System\Config $systemConfig */
        $systemConfig = ObjectManager::getInstance()->create('Mtf\System\Config');
        $superAdminPassword = $systemConfig->getConfigParam('application/backend_user_credentials/password');
        $this->_data = array(
            'fields' => array(
                'email' => array(
                    'value' => 'email%isolation%@example.com'
                ),
                'firstname' => array(
                    'value' => 'firstname%isolation%'
                ),
                'lastname' => array(
                    'value' => 'lastname%isolation%'
                ),
                'password' => array(
                    'value' => '%password%'
                ),
                'password_confirmation' => array(
                    'value' => '%password%'
                ),
                'roles' => array(
                    'value' => array('1')
                ),
                'username' => array(
                    'value' => 'admin%isolation%'
                ),
                'current_password' => array(
                    'value' => $superAdminPassword
                ),
            ),
        );
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('fields/email/value');
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getData('fields/password/value');
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getData('fields/username/value');
    }

    /**
     * Create user
     */
    public function persist()
    {
        Factory::getApp()->magentoUserCreateUser($this);
    }

    /**
     * Set password for user
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->_data['fields']['password']['value'] = $password;
        $this->_data['fields']['password_confirmation']['value'] = $password;
    }
}

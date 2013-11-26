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

class AdminUser extends DataFixture
{

    /**
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);
        $this->_placeholders['role_id'] = array($this, 'roleProvider');

    }

    //TODO implement provider
    protected function roleProvider()
    {
         return '1';
    }

    /**
     * initialize data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'email' => array(
                    'value' => 'test%isolation%@example.com'
                ),
                'firstname' => array(
                    'value' => 'test%isolation%'
                ),
                'lastname' => array(
                    'value' => 'test%isolation%'
                ),
                'password' => array(
                    'value' => '123123q'
                ),
                'password_confirmation' => array(
                    'value' => '123123q'
                ),
                'roles' => array(
                    'value' => array('1')
                ),
                'username' => array(
                    'value' => 'test%isolation%'
                ),
            ),
        );
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoUserAdminUser($this->_dataConfig, $this->_data);
    }

    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('fields/email/value');
    }

    /**
     * Create user
     */
    public function persist()
    {
        Factory::getApp()->magentoUserCreateUser($this);
    }
}
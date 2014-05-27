<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Logging
 */
class Logging extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Logging\Test\Repository\Logging';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Logging\Test\Handler\Logging\LoggingInterface';

    protected $defaultDataSet = [
        'created' => null,
        'is_active' => null,
        'interface_locale' => null,
    ];

    protected $user_id = [
        'attribute_code' => 'user_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $firstname = [
        'attribute_code' => 'firstname',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $lastname = [
        'attribute_code' => 'lastname',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $email = [
        'attribute_code' => 'email',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $username = [
        'attribute_code' => 'username',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $password = [
        'attribute_code' => 'password',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $created = [
        'attribute_code' => 'created',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => 'CURRENT_TIMESTAMP',
        'input' => '',
    ];

    protected $modified = [
        'attribute_code' => 'modified',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $logdate = [
        'attribute_code' => 'logdate',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $lognum = [
        'attribute_code' => 'lognum',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $reload_acl_flag = [
        'attribute_code' => 'reload_acl_flag',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $extra = [
        'attribute_code' => 'extra',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $rp_token = [
        'attribute_code' => 'rp_token',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $rp_token_created_at = [
        'attribute_code' => 'rp_token_created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $interface_locale = [
        'attribute_code' => 'interface_locale',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'en_US',
        'input' => '',
    ];

    protected $failures_num = [
        'attribute_code' => 'failures_num',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $first_failure = [
        'attribute_code' => 'first_failure',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $lock_expires = [
        'attribute_code' => 'lock_expires',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $aggregated_information = [
        'attribute_code' => 'aggregated_information',
        'backend_type' => 'virtual',
    ];

    public function getUserId()
    {
        return $this->getData('user_id');
    }

    public function getFirstname()
    {
        return $this->getData('firstname');
    }

    public function getLastname()
    {
        return $this->getData('lastname');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getUsername()
    {
        return $this->getData('username');
    }

    public function getPassword()
    {
        return $this->getData('password');
    }

    public function getCreated()
    {
        return $this->getData('created');
    }

    public function getModified()
    {
        return $this->getData('modified');
    }

    public function getLogdate()
    {
        return $this->getData('logdate');
    }

    public function getLognum()
    {
        return $this->getData('lognum');
    }

    public function getReloadAclFlag()
    {
        return $this->getData('reload_acl_flag');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getExtra()
    {
        return $this->getData('extra');
    }

    public function getRpToken()
    {
        return $this->getData('rp_token');
    }

    public function getRpTokenCreatedAt()
    {
        return $this->getData('rp_token_created_at');
    }

    public function getInterfaceLocale()
    {
        return $this->getData('interface_locale');
    }

    public function getFailuresNum()
    {
        return $this->getData('failures_num');
    }

    public function getFirstFailure()
    {
        return $this->getData('first_failure');
    }

    public function getLockExpires()
    {
        return $this->getData('lock_expires');
    }

    public function getAggregatedInformation()
    {
        return $this->getData('aggregated_information');
    }
}

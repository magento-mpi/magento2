<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CustomerInjectable
 *
 * @package Magento\Customer\Test\Fixture
 */
class CustomerInjectable extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Customer\Test\Repository\CustomerInjectable';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Customer\Test\Handler\CustomerInjectable\CustomerInjectableInterface';

    protected $defaultDataSet = [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'John.Doe%isolation%@example.com',
    ];

    protected $confirmation = [
        'attribute_code' => 'confirmation',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'static',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
    ];

    protected $created_in = [
        'attribute_code' => 'created_in',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $default_billing = [
        'attribute_code' => 'default_billing',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $default_shipping = [
        'attribute_code' => 'default_shipping',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $disable_auto_group_change = [
        'attribute_code' => 'disable_auto_group_change',
        'backend_type' => 'static',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'boolean',
        'group' => 'account_information',
    ];

    protected $dob = [
        'attribute_code' => 'dob',
        'backend_type' => 'datetime',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
        'group' => 'account_information',
    ];

    protected $email = [
        'attribute_code' => 'email',
        'backend_type' => 'static',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $firstname = [
        'attribute_code' => 'firstname',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $gender = [
        'attribute_code' => 'gender',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
        'group' => 'account_information',
    ];

    protected $group_id = [
        'attribute_code' => 'group_id',
        'backend_type' => 'static',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'group' => 'account_information',
    ];

    protected $lastname = [
        'attribute_code' => 'lastname',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $middlename = [
        'attribute_code' => 'middlename',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $password_hash = [
        'attribute_code' => 'password_hash',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'hidden',
    ];

    protected $prefix = [
        'attribute_code' => 'prefix',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $rp_token = [
        'attribute_code' => 'rp_token',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'hidden',
    ];

    protected $rp_token_created_at = [
        'attribute_code' => 'rp_token_created_at',
        'backend_type' => 'datetime',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'static',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
    ];

    protected $suffix = [
        'attribute_code' => 'suffix',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $taxvat = [
        'attribute_code' => 'taxvat',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'account_information',
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'static',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'group' => 'account_information',
    ];

    protected $is_subscribed = [
        'attribute_code' => 'is_subscribed',
        'backend_type' => 'virtual',
    ];

    protected $password = [
        'attribute_code' => 'password',
        'backend_type' => 'virtual',
    ];

    protected $password_confirmation = [
        'attribute_code' => 'password_confirmation',
        'backend_type' => 'virtual',
    ];

    public function getConfirmation()
    {
        return $this->getData('confirmation');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getCreatedIn()
    {
        return $this->getData('created_in');
    }

    public function getDefaultBilling()
    {
        return $this->getData('default_billing');
    }

    public function getDefaultShipping()
    {
        return $this->getData('default_shipping');
    }

    public function getDisableAutoGroupChange()
    {
        return $this->getData('disable_auto_group_change');
    }

    public function getDob()
    {
        return $this->getData('dob');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getFirstname()
    {
        return $this->getData('firstname');
    }

    public function getGender()
    {
        return $this->getData('gender');
    }

    public function getGroupId()
    {
        return $this->getData('group_id');
    }

    public function getLastname()
    {
        return $this->getData('lastname');
    }

    public function getMiddlename()
    {
        return $this->getData('middlename');
    }

    public function getPasswordHash()
    {
        return $this->getData('password_hash');
    }

    public function getPrefix()
    {
        return $this->getData('prefix');
    }

    public function getRpToken()
    {
        return $this->getData('rp_token');
    }

    public function getRpTokenCreatedAt()
    {
        return $this->getData('rp_token_created_at');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getSuffix()
    {
        return $this->getData('suffix');
    }

    public function getTaxvat()
    {
        return $this->getData('taxvat');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getIsSubscribed()
    {
        return $this->getData('is_subscribed');
    }

    public function getPassword()
    {
        return $this->getData('password');
    }

    public function getPasswordConfirmation()
    {
        return $this->getData('password_confirmation');
    }
}

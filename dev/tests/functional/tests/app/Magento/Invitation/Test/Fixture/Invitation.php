<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Invitation.
 * Fixture for Invitation.
 */
class Invitation extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Invitation\Test\Repository\Invitation';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Invitation\Test\Handler\Invitation\InvitationInterface';

    protected $defaultDataSet = [
        'email' => 'test@example.com',
        'message' => 'test message',
        'status' => null,
    ];

    protected $invitation_id = [
        'attribute_code' => 'invitation_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_id = [
        'attribute_code' => 'customer_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $invitation_date = [
        'attribute_code' => 'invitation_date',
        'backend_type' => 'timestamp',
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
        'source' => 'Magento\Invitation\Test\Fixture\Invitation\Email'
    ];

    protected $referral_id = [
        'attribute_code' => 'referral_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $protection_code = [
        'attribute_code' => 'protection_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $signup_date = [
        'attribute_code' => 'signup_date',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Invitation\Test\Fixture\Invitation\StoreId',
    ];

    protected $group_id = [
        'attribute_code' => 'group_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Invitation\Test\Fixture\Invitation\GroupId'
    ];

    protected $message = [
        'attribute_code' => 'message',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'new',
        'input' => '',
    ];

    public function getInvitationId()
    {
        return $this->getData('invitation_id');
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function getInvitationDate()
    {
        return $this->getData('invitation_date');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getReferralId()
    {
        return $this->getData('referral_id');
    }

    public function getProtectionCode()
    {
        return $this->getData('protection_code');
    }

    public function getSignupDate()
    {
        return $this->getData('signup_date');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getGroupId()
    {
        return $this->getData('group_id');
    }

    public function getMessage()
    {
        return $this->getData('message');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }
}

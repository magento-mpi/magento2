<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class GiftCardAccount
 * Gift card account fixture
 */
class GiftCardAccount extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftCardAccount\Test\Repository\GiftCardAccount';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftCardAccount\Test\Handler\GiftCardAccount\GiftCardAccountInterface';

    protected $defaultDataSet = [
        'balance' => 0,
        'website_id' => ['dataSet' => 'Main Website'],
    ];

    protected $giftcardaccount_id = [
        'attribute_code' => 'giftcardaccount_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $code = [
        'attribute_code' => 'code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $date_created = [
        'attribute_code' => 'date_created',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $date_expires = [
        'attribute_code' => 'date_expires',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'source' => 'Magento\GiftCardAccount\Test\Fixture\GiftCardAccount\WebsiteId',
    ];

    protected $balance = [
        'attribute_code' => 'balance',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $state = [
        'attribute_code' => 'state',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $is_redeemable = [
        'attribute_code' => 'is_redeemable',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    public function getGiftcardaccountId()
    {
        return $this->getData('giftcardaccount_id');
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getDateCreated()
    {
        return $this->getData('date_created');
    }

    public function getDateExpires()
    {
        return $this->getData('date_expires');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getBalance()
    {
        return $this->getData('balance');
    }

    public function getState()
    {
        return $this->getData('state');
    }

    public function getIsRedeemable()
    {
        return $this->getData('is_redeemable');
    }
}

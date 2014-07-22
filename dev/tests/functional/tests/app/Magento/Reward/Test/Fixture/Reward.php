<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Reward
 * Reward point fixture
 */
class Reward extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Reward\Test\Repository\Reward';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Reward\Test\Handler\Reward\RewardInterface';

{
    protected $defaultDataSet = [
        'website_id' => 'Main Website',
        'customer_group_id' => 'All Customer Groups',
        'direction' => 'Points to Currency',
        'value' => 10,
        'equal_value' => 1
    ];

    protected $reward_id = [
        'attribute_code' => 'reward_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_id = [
        'attribute_code' => 'customer_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $points_balance = [
        'attribute_code' => 'points_balance',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $website_currency_code = [
        'attribute_code' => 'website_currency_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $rate_id = [
        'attribute_code' => 'rate_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_group_id = [
        'attribute_code' => 'customer_group_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $direction = [
        'attribute_code' => 'direction',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $points = [
        'attribute_code' => 'points',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $currency_amount = [
        'attribute_code' => 'currency_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $reward_update_notification = [
        'attribute_code' => 'reward_update_notification',
        'backend_type' => 'virtual',
    ];

    protected $reward_warning_notification = [
        'attribute_code' => 'reward_warning_notification',
        'backend_type' => 'virtual',
    ];

    protected $value = [
        'attribute_code' => 'value',
        'backend_type' => 'virtual',
    ];

    protected $equal_value = [
        'attribute_code' => 'equal_value',
        'backend_type' => 'virtual',
    ];

    public function getRewardId()
    {
        return $this->getData('reward_id');
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getPointsBalance()
    {
        return $this->getData('points_balance');
    }

    public function getWebsiteCurrencyCode()
    {
        return $this->getData('website_currency_code');
    }

    public function getRateId()
    {
        return $this->getData('rate_id');
    }

    public function getCustomerGroupId()
    {
        return $this->getData('customer_group_id');
    }

    public function getDirection()
    {
        return $this->getData('direction');
    }

    public function getPoints()
    {
        return $this->getData('points');
    }

    public function getCurrencyAmount()
    {
        return $this->getData('currency_amount');
    }

    public function getSubscribeUpdates()
    {
        return $this->getData('subscribe_updates');
    }

    public function getSubscribeWarnings()
    {
        return $this->getData('subscribe_warnings');
    }

    public function getValue()
    {
        return $this->getData('value');
    }

    public function getEqualValue()
    {
        return $this->getData('equal_value');
    }
}

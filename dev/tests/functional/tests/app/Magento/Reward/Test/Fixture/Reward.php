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

    protected $defaultDataSet = [
        'points_delta' => 10,
        'customer_id' => ['dataSet' => 'default'],
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
        'source' => 'Magento\Reward\Test\Fixture\Reward\CustomerId'
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'reward_points'
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

    protected $reward_update_notification = [
        'attribute_code' => 'reward_update_notification',
        'backend_type' => 'virtual',
    ];

    protected $reward_warning_notification = [
        'attribute_code' => 'reward_warning_notification',
        'backend_type' => 'virtual',
    ];

    protected $points_delta = [
        'attribute_code' => 'points_delta',
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

    public function getRewardUpdateNotification()
    {
        return $this->getData('reward_update_notification');
    }

    public function getRewardWarningNotification()
    {
        return $this->getData('reward_warning_notification');
    }

    public function getPointsDelta()
    {
        return $this->getData('points_delta');
    }
}

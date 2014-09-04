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
 * Class RewardRate
 * Reward points rate fixture
 */
class RewardRate extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Reward\Test\Repository\RewardRate';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Reward\Test\Handler\RewardRate\RewardRateInterface';

    protected $defaultDataSet = [
        'website_id' => ['dataSet' => 'Main Website'],
        'customer_group_id' => ['dataSet' => 'All Customer Groups'],
        'direction' => 'Points to Currency',
        'value' => 10,
        'equal_value' => 1
    ];

    protected $rate_id = [
        'attribute_code' => 'rate_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'source' => 'Magento\Reward\Test\Fixture\RewardRate\WebsiteId',
    ];

    protected $customer_group_id = [
        'attribute_code' => 'customer_group_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'source' => 'Magento\Reward\Test\Fixture\RewardRate\CustomerGroup'
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

    protected $value = [
        'attribute_code' => 'value',
        'backend_type' => 'virtual',
    ];

    protected $equal_value = [
        'attribute_code' => 'equal_value',
        'backend_type' => 'virtual',
    ];

    public function getRateId()
    {
        return $this->getData('rate_id');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
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

    public function getValue()
    {
        return $this->getData('value');
    }

    public function getEqualValue()
    {
        return $this->getData('equal_value');
    }
}

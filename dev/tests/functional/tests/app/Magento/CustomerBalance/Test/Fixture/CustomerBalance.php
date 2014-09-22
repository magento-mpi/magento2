<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CustomerBalance
 * Customer balance fixture
 */
class CustomerBalance extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CustomerBalance\Test\Repository\CustomerBalance';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CustomerBalance\Test\Handler\CustomerBalance\CustomerBalanceInterface';

    protected $defaultDataSet = [
        'balance_delta' => 100,
        'website_id' => ['dataSet' => 'main_website'],
        'additional_info' => 'Some comment',
    ];

    protected $balance_id = [
        'attribute_code' => 'balance_id',
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
        'source' => 'Magento\CustomerBalance\Test\Fixture\CustomerBalance\CustomerId',
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'group' => 'store_credit',
        'input' => 'select',
        'source' => 'Magento\CustomerBalance\Test\Fixture\CustomerBalance\WebsiteId',
    ];

    protected $amount = [
        'attribute_code' => 'amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $base_currency_code = [
        'attribute_code' => 'base_currency_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $balance_delta = [
        'attribute_code' => 'balance_delta',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'group' => 'store_credit',
        'input' => '',
    ];

    protected $additional_info = [
        'attribute_code' => 'additional_info',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'group' => 'store_credit',
        'input' => '',
    ];

    protected $is_customer_notified = [
        'attribute_code' => 'is_customer_notified',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'group' => 'store_credit',
        'input' => '',
    ];

    public function getBalanceId()
    {
        return $this->getData('balance_id');
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getAmount()
    {
        return $this->getData('amount');
    }

    public function getBaseCurrencyCode()
    {
        return $this->getData('base_currency_code');
    }

    public function getBalanceDelta()
    {
        return $this->getData('balance_delta');
    }

    public function getAdditionalInfo()
    {
        return $this->getData('additional_info');
    }

    public function getIsCustomerNotified()
    {
        return $this->getData('is_customer_notified');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CatalogRule
 *
 * @package Magento\CatalogRule\Test\Fixture
 */
class CatalogRule extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CatalogRule\Test\Repository\CatalogRule';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CatalogRule\Test\Handler\CatalogRule\CatalogRuleInterface';

    protected $defaultDataSet = [
        'name' => 'CatalogPriceRule %isolation%',
        'description' => 'Catalog Price Rule Description',
        'is_active' => 'Active',
        'website_ids' => 'Main Website',
        'customer_group_ids' => 'NOT LOGGED IN',
        'simple_action' => 'By Percentage of the Original Price',
        'discount_amount' => '50'
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group' => 'rule_information',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'default_value' => '',
        'input' => 'text',
        'group' => 'rule_information',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'group' => 'rule_information',
    ];

    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'multiselect',
        'group' => 'rule_information',
    ];

    protected $customer_group_ids = [
        'attribute_code' => 'customer_group_ids',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'multiselect',
        'group' => 'rule_information',
    ];

    protected $simple_action = [
        'attribute_code' => 'simple_action',
        'backend_type' => 'smallint',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
        'group' => 'actions',
    ];

    protected $discount_amount = [
        'attribute_code' => 'discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group' => 'actions',
    ];

    protected $condition_type = [
        'attribute_code' => 'condition_type',
        'backend_type' => 'virtual',
        'is_required' => '0',
        'group' => 'conditions',
        'input' => 'select',
    ];

    protected $condition_value = [
        'attribute_code' => 'condition_value',
        'backend_type' => 'virtual',
        'is_required' => '0',
        'group' => 'conditions',
        'fixture' => 'Magento\CatalogRule\Test\Fixture\Conditions',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    public function getName()
    {
        return $this->getData('name');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }

    public function getCustomerGroupIds()
    {
        return $this->getData('customer_group_ids');
    }

    public function getSimpleAction()
    {
        return $this->getData('simple_action');
    }

    public function getDiscountAmount()
    {
        return $this->getData('discount_amount');
    }

    public function getConditionType()
    {
        return $this->getData('condition_type');
    }

    public function getConditionValue()
    {
        return $this->getData('condition_value');
    }

    public function getId()
    {
        return $this->getData('id');
    }
}

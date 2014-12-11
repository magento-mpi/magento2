<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class TargetRule
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class TargetRule extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\TargetRule\Test\Repository\TargetRule';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\TargetRule\Test\Handler\TargetRule\TargetRuleInterface';

    protected $defaultDataSet = [
        'name' => 'TargetRule%isolation%',
        'is_active' => 'Active',
        'apply_to' => 'Related Products',
        'use_customer_segment' => 'All',
    ];

    protected $rule_id = [
        'attribute_code' => 'rule_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $from_date = [
        'attribute_code' => 'from_date',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Backend\Test\Fixture\Date',
        'group' => 'rule_information',
    ];

    protected $to_date = [
        'attribute_code' => 'to_date',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Backend\Test\Fixture\Date',
        'group' => 'rule_information',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $conditions_serialized = [
        'attribute_code' => 'conditions_serialized',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'products_to_match',
    ];

    protected $actions_serialized = [
        'attribute_code' => 'actions_serialized',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'products_to_display',
    ];

    protected $positions_limit = [
        'attribute_code' => 'positions_limit',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $apply_to = [
        'attribute_code' => 'apply_to',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $use_customer_segment = [
        'attribute_code' => 'use_customer_segment',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $customer_segment_ids = [
        'attribute_code' => 'customer_segment_ids',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $action_select = [
        'attribute_code' => 'action_select',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $action_select_bind = [
        'attribute_code' => 'action_select_bind',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getRuleId()
    {
        return $this->getData('rule_id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    public function getToDate()
    {
        return $this->getData('to_date');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    public function getActionsSerialized()
    {
        return $this->getData('actions_serialized');
    }

    public function getPositionsLimit()
    {
        return $this->getData('positions_limit');
    }

    public function getApplyTo()
    {
        return $this->getData('apply_to');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getUseCustomerSegment()
    {
        return $this->getData('use_customer_segment');
    }

    public function getCustomerSegmentIds()
    {
        return $this->getData('customer_segment_ids');
    }

    public function getActionSelect()
    {
        return $this->getData('action_select');
    }

    public function getActionSelectBind()
    {
        return $this->getData('action_select_bind');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Fixture Reminder
 */
class Reminder extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Reminder\Test\Repository\Reminder';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Reminder\Test\Handler\Reminder\ReminderInterface';

    /**
     * Default data set.
     *
     * @var array
     */
    protected $defaultDataSet = [
        'name' => 'Reminder%isolation%',
        'is_active' => 'Active',
        'conditions_serialized' => '[Shopping Cart|for| |ALL]'
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

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $conditions_serialized = [
        'attribute_code' => 'conditions_serialized',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'conditions',
    ];

    protected $condition_sql = [
        'attribute_code' => 'condition_sql',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $salesrule_id = [
        'attribute_code' => 'salesrule_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => '\Magento\Reminder\Test\Fixture\Reminder\SalesruleId',
        'group' => 'rule_information',
    ];

    protected $schedule = [
        'attribute_code' => 'schedule',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'rule_information',
    ];

    protected $default_label = [
        'attribute_code' => 'default_label',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $default_description = [
        'attribute_code' => 'default_description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $from_date = [
        'attribute_code' => 'from_date',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => '\Magento\Backend\Test\Fixture\Date',
        'group' => 'rule_information',
    ];

    protected $to_date = [
        'attribute_code' => 'to_date',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => '\Magento\Backend\Test\Fixture\Date',
        'group' => 'rule_information',
    ];

    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'virtual',
        'group' => 'rule_information',
    ];

    public function getRuleId()
    {
        return $this->getData('rule_id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    public function getConditionSql()
    {
        return $this->getData('condition_sql');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getSalesruleId()
    {
        return $this->getData('salesrule_id');
    }

    public function getSchedule()
    {
        return $this->getData('schedule');
    }

    public function getDefaultLabel()
    {
        return $this->getData('default_label');
    }

    public function getDefaultDescription()
    {
        return $this->getData('default_description');
    }

    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    public function getToDate()
    {
        return $this->getData('to_date');
    }

    public function getWebsiteIds()
    {
        return $this->getDefaultLabel('website_ids');
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CustomerSegment
 * Customer Segment fixture
 */
class CustomerSegment extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CustomerSegment\Test\Repository\CustomerSegment';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CustomerSegment\Test\Handler\CustomerSegment\CustomerSegmentInterface';

    /**
     * @var array
     */
    protected $defaultDataSet = [
        'name' => 'Test Customer Segment %isolation%',
        'description' => 'Test Customer Segment Description %isolation%',
        'website_ids' => [
            0 => 'Main Website',
        ],
        'is_active' => 'Active',
        'apply_to' => 'Visitors and Registered Customers',
    ];

    protected $segment_id = [
        'attribute_code' => 'segment_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
        'group' => null,
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'general_properties',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'general_properties',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'general_properties',
    ];

    protected $conditions_serialized = [
        'attribute_code' => 'conditions_serialized',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'conditions',
    ];

    protected $processing_frequency = [
        'attribute_code' => 'processing_frequency',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $condition_sql = [
        'attribute_code' => 'condition_sql',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $apply_to = [
        'attribute_code' => 'apply_to',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'general_properties',
    ];

    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'virtual',
        'group' => 'general_properties',
    ];

    public function getSegmentId()
    {
        return $this->getData('segment_id');
    }

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

    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    public function getProcessingFrequency()
    {
        return $this->getData('processing_frequency');
    }

    public function getConditionSql()
    {
        return $this->getData('condition_sql');
    }

    public function getApplyTo()
    {
        return $this->getData('apply_to');
    }

    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }
}

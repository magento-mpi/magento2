<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CustomerCustomAttribute
 * Fixture with all necessary data for custom Customer Attribute creation on backend
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CustomerCustomAttribute extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CustomerCustomAttributes\Test\Repository\CustomerCustomAttribute';

    // @codingStandardsIgnoreStart
    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CustomerCustomAttributes\Test\Handler\CustomerCustomAttribute\CustomerCustomAttributeInterface';
    // @codingStandardsIgnoreEnd

    protected $defaultDataSet = [
        'frontend_label' => 'attribute_%isolation%',
        'attribute_code' => 'attribute_%isolation%',
        'frontend_input' => 'Text Field',
        'sort_order' => '10'
    ];

    protected $attribute_id = [
        'attribute_code' => 'attribute_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
        'group' => null
    ];

    protected $entity_type_id = [
        'attribute_code' => 'entity_type_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $attribute_code = [
        'attribute_code' => 'attribute_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'properties',
    ];

    protected $attribute_model = [
        'attribute_code' => 'attribute_model',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $backend_model = [
        'attribute_code' => 'backend_model',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $backend_type = [
        'attribute_code' => 'backend_type',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'static',
        'input' => '',
    ];

    protected $backend_table = [
        'attribute_code' => 'backend_table',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $frontend_model = [
        'attribute_code' => 'frontend_model',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $frontend_input = [
        'attribute_code' => 'frontend_input',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $frontend_label = [
        'attribute_code' => 'frontend_label',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'properties',
    ];

    protected $frontend_class = [
        'attribute_code' => 'frontend_class',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $source_model = [
        'attribute_code' => 'source_model',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_required = [
        'attribute_code' => 'is_required',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $is_user_defined = [
        'attribute_code' => 'is_user_defined',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $default_value = [
        'attribute_code' => 'default_value',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_unique = [
        'attribute_code' => 'is_unique',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $note = [
        'attribute_code' => 'note',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_visible = [
        'attribute_code' => 'is_visible',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $input_filter = [
        'attribute_code' => 'input_filter',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $multiline_count = [
        'attribute_code' => 'multiline_count',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $validate_rules = [
        'attribute_code' => 'validate_rules',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_system = [
        'attribute_code' => 'is_system',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'group' => 'properties',
    ];

    protected $data_model = [
        'attribute_code' => 'data_model',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_used_for_customer_segment = [
        'attribute_code' => 'is_used_for_customer_segment',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $scope_default_value_text = [
        'attribute_code' => 'scope_default_value_text',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $scope_date_range_min = [
        'attribute_code' => 'scope_date_range_min',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $scope_date_range_max = [
        'attribute_code' => 'scope_date_range_max',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $input_validation = [
        'attribute_code' => 'input_validation',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $min_text_length = [
        'attribute_code' => 'min_text_length',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $max_text_length = [
        'attribute_code' => 'max_text_length',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $scope_multiline_count = [
        'attribute_code' => 'scope_multiline_count',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $max_file_size = [
        'attribute_code' => 'max_file_size',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $file_extensions = [
        'attribute_code' => 'file_extensions',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $max_image_width = [
        'attribute_code' => 'max_image_width',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $max_image_heght = [
        'attribute_code' => 'max_image_heght',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
        'group' => 'properties',
    ];

    protected $scope_is_visible = [
        'attribute_code' => 'scope_is_visible',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $used_in_forms = [
        'attribute_code' => 'used_in_forms',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'multiselectgrouplist',
        'group' => 'properties',
    ];

    protected $scope_is_required = [
        'attribute_code' => 'scope_is_required',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'properties',
    ];

    protected $manage_title = [
        'attribute_code' => 'manage_title',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'manage_options',
    ];

    protected $option = [
        'attribute_code' => 'option',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => '\Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute\Option',
        'group' => 'manage_options',
    ];

    public function getAttributeId()
    {
        return $this->getData('attribute_id');
    }

    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

    public function getAttributeCode()
    {
        return $this->getData('attribute_code');
    }

    public function getAttributeModel()
    {
        return $this->getData('attribute_model');
    }

    public function getBackendModel()
    {
        return $this->getData('backend_model');
    }

    public function getBackendType()
    {
        return $this->getData('backend_type');
    }

    public function getBackendTable()
    {
        return $this->getData('backend_table');
    }

    public function getFrontendModel()
    {
        return $this->getData('frontend_model');
    }

    public function getFrontendInput()
    {
        return $this->getData('frontend_input');
    }

    public function getFrontendLabel()
    {
        return $this->getData('frontend_label');
    }

    public function getFrontendClass()
    {
        return $this->getData('frontend_class');
    }

    public function getSourceModel()
    {
        return $this->getData('source_model');
    }

    public function getIsRequired()
    {
        return $this->getData('is_required');
    }

    public function getIsUserDefined()
    {
        return $this->getData('is_user_defined');
    }

    public function getDefaultValue()
    {
        return $this->getData('default_value');
    }

    public function getIsUnique()
    {
        return $this->getData('is_unique');
    }

    public function getNote()
    {
        return $this->getData('note');
    }

    public function getIsVisible()
    {
        return $this->getData('is_visible');
    }

    public function getInputFilter()
    {
        return $this->getData('input_filter');
    }

    public function getMultilineCount()
    {
        return $this->getData('multiline_count');
    }

    public function getValidateRules()
    {
        return $this->getData('validate_rules');
    }

    public function getIsSystem()
    {
        return $this->getData('is_system');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getDataModel()
    {
        return $this->getData('data_model');
    }

    public function getIsUsedForCustomerSegment()
    {
        return $this->getData('is_used_for_customer_segment');
    }

    public function getScopeDefaultValueText()
    {
        return $this->getData('scope_default_value_text');
    }

    public function getScopeDateRangeMin()
    {
        return $this->getData('scope_date_range_min');
    }

    public function getScopeDateRangeMax()
    {
        return $this->getData('scope_date_range_max');
    }

    public function getInputValidation()
    {
        return $this->getData('input_validation');
    }

    public function getMinTextLength()
    {
        return $this->getData('min_text_length');
    }

    public function getMaxTextLength()
    {
        return $this->getData('max_text_length');
    }

    public function getScopeMultilineCount()
    {
        return $this->getData('scope_multiline_count');
    }

    public function getMaxFileSize()
    {
        return $this->getData('max_file_size');
    }

    public function getFileExtensions()
    {
        return $this->getData('file_extensions');
    }

    public function getMaxImageWidth()
    {
        return $this->getData('max_image_width');
    }

    public function getMaxImageHeght()
    {
        return $this->getData('max_image_heght');
    }

    public function getScopeIsVisible()
    {
        return $this->getData('scope_is_visible');
    }

    public function getUsedInForms()
    {
        return $this->getData('used_in_forms');
    }

    public function getScopeIsRequired()
    {
        return $this->getData('scope_is_required');
    }

    public function getManageTitle()
    {
        return $this->getData('manage_title');
    }
}

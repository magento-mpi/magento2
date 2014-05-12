<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CustomerGroup
 *
 * @package Magento\Customer\Test\Fixture
 */
class CustomerGroup extends InjectableFixture
{
    protected $defaultDataSet = [
        'customer_group_code' => 'customer_code_%isolation%',
        'tax_class_id' => 'Retail Customer',
    ];

    protected $customer_group_code = [
        'attribute_code' => 'code',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $tax_class_id = [
        'attribute_code' => 'tax_class',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'fixture' => 'Magento\Customer\Test\Fixture\CustomerGroup\TaxClassIds',
    ];

    public function getCustomerGroupCode()
    {
        return $this->getData('customer_group_code');
    }

    public function getTaxClassId()
    {
        return $this->getData('tax_class_id');
    }
}

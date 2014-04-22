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
        'code' => 'customer_code_%isolation%',
    ];

    protected $code = [
        'attribute_code' => 'code',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $tax_class = [
        'attribute_code' => 'tax_class',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'fixture' => 'Magento\Customer\Test\Fixture\CustomerGroup\TaxClassIds',
    ];

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getTaxClass()
    {
        return $this->getData('tax_class');
    }
}

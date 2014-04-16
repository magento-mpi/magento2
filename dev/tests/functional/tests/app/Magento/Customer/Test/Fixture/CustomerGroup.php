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
 * @package Fixture
 */
class CustomerGroup extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Customer\Test\Repository\CustomerGroup';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Customer\Test\Handler\CustomerGroup\CustomerGroupInterface';

    protected $defaultDataSet = [
        'code' => 'customer_code_%isolation%',
        'tax_class' => 'Retail Customer',
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

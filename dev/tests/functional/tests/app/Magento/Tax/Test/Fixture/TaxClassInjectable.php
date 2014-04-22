<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class TaxClassInjectable
 *
 * @package Magento\Tax\Test\Fixture
 */
class TaxClassInjectable extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Tax\Test\Repository\TaxClass';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Tax\Test\Handler\TaxClass\TaxClassInterface';

    protected $defaultDataSet = [
        'class_name' => 'TaxClass%isolation%',
        'class_type' => 'CUSTOMER'
    ];

    protected $class_id = [
        'attribute_code' => 'class_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $class_name = [
        'attribute_code' => 'class_name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'TaxClass%isolation%',
        'input' => '',
    ];

    protected $class_type = [
        'attribute_code' => 'class_type',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'CUSTOMER',
        'input' => '',
    ];

    public function getClassId()
    {
        return $this->getData('class_id');
    }

    public function getClassName()
    {
        return $this->getData('class_name');
    }

    public function getClassType()
    {
        return $this->getData('class_type');
    }

}

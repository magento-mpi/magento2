<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class SystemVariable
 *
 * @package Magento\Core\Test\Fixture
 */
class SystemVariable extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Core\Test\Repository\SystemVariable';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Core\Test\Handler\SystemVariable\SystemVariableInterface';

    protected $defaultDataSet = [
        'code' => 'variableCode%isolation%',
        'name' => 'variableName%isolation%',
        'html_value' => '{{html_value=""}}',
        'plain_value' => 'plain_value'
    ];

    protected $variable_id = [
        'attribute_code' => 'variable_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $code = [
        'attribute_code' => 'code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $value_id = [
        'attribute_code' => 'value_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $plain_value = [
        'attribute_code' => 'plain_value',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $html_value = [
        'attribute_code' => 'html_value',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getVariableId()
    {
        return $this->getData('variable_id');
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getValueId()
    {
        return $this->getData('value_id');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getPlainValue()
    {
        return $this->getData('plain_value');
    }

    public function getHtmlValue()
    {
        return $this->getData('html_value');
    }
}

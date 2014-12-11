<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CurrencySymbol\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CurrencySymbolEntity
 * Currency Symbol fixture
 */
class CurrencySymbolEntity extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CurrencySymbol\Test\Repository\CurrencySymbolEntity';

    // @codingStandardsIgnoreStart
    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CurrencySymbol\Test\Handler\CurrencySymbolEntity\CurrencySymbolEntityInterface';
    // @codingStandardsIgnoreEnd

    protected $defaultDataSet = [
        'inherit_custom_currency_symbol' => 'Yes',
    ];

    protected $config_id = [
        'attribute_code' => 'config_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $scope = [
        'attribute_code' => 'scope',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'default',
        'input' => '',
    ];

    protected $scope_id = [
        'attribute_code' => 'scope_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $path = [
        'attribute_code' => 'path',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'general',
        'input' => '',
    ];

    protected $value = [
        'attribute_code' => 'value',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $inherit_custom_currency_symbol = [
        'attribute_code' => 'inherit_custom_currency_symbol',
        'backend_type' => 'virtual',
        'input' => 'checkbox',
    ];

    protected $custom_currency_symbol = [
        'attribute_code' => 'custom_currency_symbol',
        'backend_type' => 'virtual',
        'input' => '',
    ];

    protected $code = [
        'attribute_code' => 'code',
        'backend_type' => 'virtual',
        'input' => '',
    ];

    public function getConfigId()
    {
        return $this->getData('config_id');
    }

    public function getScope()
    {
        return $this->getData('scope');
    }

    public function getScopeId()
    {
        return $this->getData('scope_id');
    }

    public function getPath()
    {
        return $this->getData('path');
    }

    public function getValue()
    {
        return $this->getData('value');
    }

    public function getInheritCustomCurrencySymbol()
    {
        return $this->getData('inherit_custom_currency_symbol');
    }

    public function getCustomCurrencySymbol()
    {
        return $this->getData('custom_currency_symbol');
    }

    public function getCode()
    {
        return $this->getData('code');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CurrencySymbolEntity
 * Data for creation Currency Symbol
 */
class CurrencySymbolEntity extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['custom'] = [
            'custom_currency_symbol' => ['UAH' => 'custom'],
            'inherit_custom_currency_symbol' => ['USD' => '1'],
        ];
    }
}

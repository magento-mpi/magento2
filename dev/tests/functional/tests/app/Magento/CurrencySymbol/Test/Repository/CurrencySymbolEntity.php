<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_data['currency_symbols_uah'] = [
            'custom_currency_symbol' => ['UAH' => 'custom'],
        ];

        $this->_data['currency_symbols_eur'] = [
            'custom_currency_symbol' => ['EUR' => '€'],
            'code' => 'EUR',
        ];
    }
}

<?php
/**
 * @spi
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CurrencySymbol\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class CurrencyRate
 * Magento currency rates
 *
 */
class CurrencyRate extends DataFixture
{
    /**
     * Persist currency rates to application
     */
    public function persist()
    {
        Factory::getApp()->magentoCurrencySymbolApplyCurrencyRates($this);
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCurrencySymbolCurrencyRate($this->_dataConfig, $this->_data);
    }
}

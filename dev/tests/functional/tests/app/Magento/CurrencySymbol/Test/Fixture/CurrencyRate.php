<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class CurrencyRate
 * Magento currency rates
 *
 * @package Magento\Core\Test\Fixture
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

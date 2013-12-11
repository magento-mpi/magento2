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

namespace Magento\Core\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Currency
 * Magento currency rates
 *
 * @package Magento\Core\Test\Fixture
 */
class Currency extends DataFixture
{
    /**
     * Persist currency rates to application
     */
    public function persist()
    {
        Factory::getApp()->magentoCoreApplyCurrencyRates($this);
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCoreCurrencyRate($this->_dataConfig, $this->_data);
    }
}

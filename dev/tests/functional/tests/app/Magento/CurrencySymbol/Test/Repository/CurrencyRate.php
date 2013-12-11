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

namespace Magento\CurrencySymbol\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CurrencyRate Repository
 * Magento currency rates
 *
 * @package Magento\Core\Test\Repository
 */
class CurrencyRate extends AbstractRepository
{
    /**
     * CurrencyRate repository constructor
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['usd_chf_rate_0_9'] = $this->_getCurrencyRateUSDCHF();
    }

    /**
     * Currency rate - US Dollars, Swiss Franc 0.9
     *
     * @return array
     */
    protected function _getCurrencyRateUSDCHF()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'rate[USD][CHF]' => array(
                        'value' => '0.9'
                    )
                )
            )
        );
    }
}

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

namespace Magento\Directory\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Currency Repository
 * Currency configuration settings
 *
 * @package Magento\Directory\Test\Repository
 */
class Currency extends AbstractRepository
{
    /**
     * Initialize repository data
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['usd_eur_rates'] = $this->_getUsdToEurRate();
    }

    /**
     * Retrieve USD to EUR rate
     *
     * @return array
     */
    protected function _getUsdToEurRate()
    {
        $data = array();
        $data['data']['rate']['USD']['EUR'] = 0.8;
        $data['data']['rate']['USD']['USD'] = 1.0;

        return $data;
    }
}

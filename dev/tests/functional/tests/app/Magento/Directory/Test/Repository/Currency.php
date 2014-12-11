<?php
/**
 * @spi
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Directory\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Currency Repository
 * Currency configuration settings
 *
 */
class Currency extends AbstractRepository
{
    /**
     * Initialize repository data
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'config' => $defaultConfig,
            'data' => $defaultData,
        ];

        $this->_data['usd_eur_rates'] = $this->_getUsdToEurRate();
    }

    /**
     * Retrieve USD to EUR rate
     *
     * @return array
     */
    protected function _getUsdToEurRate()
    {
        $data = [];
        $data['data']['rate']['USD']['EUR'] = 0.8;
        $data['data']['rate']['USD']['USD'] = 1.0;

        return $data;
    }
}

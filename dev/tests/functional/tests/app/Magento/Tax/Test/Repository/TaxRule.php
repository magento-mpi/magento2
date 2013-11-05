<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Tax Rule Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class TaxRule extends AbstractRepository
{
    /**
     * Initialize repository data
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

        $this->_data['custom_rule'] = array_replace_recursive($this->_data['default'], $this->_getCustomTaxRule());
    }

    /**
     * Return data structure for Tax Rule with custom Rates, Tax class
     *
     * @return array
     */
    protected function _getCustomTaxRule()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'tax_rate[0]' => array(
                        'value' => '%us_ca_rate_8_25%'
                    ),
                    'tax_rate[1]' => array(
                        'value' => '%us_ny_rate_8_375%'
                    ),
                )
            )
        );
    }
}

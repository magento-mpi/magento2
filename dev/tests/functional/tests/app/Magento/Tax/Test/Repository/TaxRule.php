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
        $this->_data['us_ca_ny_rule'] = $this->_getUscanyTaxRule();
        $this->_data['uk_full_tax_rule'] = $this->getUKFullTaxRule($this->_data['default']);
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

    /**
     * Return data structure for Tax Rule with custom Rates, Tax classes
     *
     * @return array
     */
    protected function _getUscanyTaxRule()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'code' => array(
                        'value' => 'Tax Rule %isolation%'
                    ),
                    'tax_rate' => array(
                        array(
                            'code' => array(
                                'value' => 'US-CA-*-Rate 1'
                            )
                        ),
                        array(
                            'code' => array(
                                'value' => 'US-NY-*-%isolation%'
                            ),
                            'rate' => array(
                                'value' => '8.1'
                            ),
                            'tax_region_id' => array(
                                'value' => 'New York',
                                'input' => 'select'
                            )
                        )
                    ),
                    'tax_customer_class' => array(
                        'value' => array(
                            'Retail Customer',
                            'Customer Tax Class %isolation%'
                        )
                    ),
                    'tax_product_class' => array(
                        'value' => array(
                            'Taxable Goods',
                            'Product Tax Class %isolation%'
                        )
                    ),
                    'priority' => array(
                        'value' => '0'
                    ),
                    'position' => array(
                        'value' => '0'
                    )
                )
            )
        );
    }

    /**
     * Get UK full tax rule
     *
     * @param array $defaultData
     * @return array
     */
    protected function getUKFullTaxRule($defaultData)
    {
        return array_replace_recursive($defaultData, array(
            'data' => array(
                'fields' => array(
                    'tax_rate' => array(
                        'value' => '%uk_full_tax_rate%'
                    ),
                ),
            ),
        ));
    }
}

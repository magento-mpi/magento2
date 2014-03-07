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
 * Class Tax Rate Repository
 *
 * @package Magento\Tax\Test\Repository
 */
class TaxRate extends AbstractRepository
{
    /**
     * Initialize default parameters
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

        $this->_data['us_ca_rate_8_25'] = array_replace_recursive($this->_data['default'], $this->_getRateUSCA());
        $this->_data['us_ny_rate_8_375'] = array_replace_recursive($this->_data['default'], $this->_getRateUSNY());
        $this->_data['us_ny_rate_8_1'] = array_replace_recursive($this->_data['default'], $this->_getRateUSNYCustom());
        $this->_data['paypal_rate_8_25'] = array_replace_recursive($this->_data['default'], $this->_getRatePayPal());
        $this->_data['uk_full_tax_rate'] = $this->getUKFullTaxRate($this->_data['default']);
    }

    /**
     * Rate US CA with 8.25%
     *
     * @return array
     */
    protected function _getRateUSCA()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'rate' => array(
                        'value' => '8.25'
                    ),
                    'tax_postcode' => array(
                        'value' => '90230'
                    ),
                    'tax_region_id' => array(
                        'value' => '12' // California
                    )
                )
            )
        );
    }

    /**
     * Rate US CA with 8.25%
     *
     * @return array
     */
    protected function _getRatePayPal()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'rate' => array(
                        'value' => '8.25'
                    ),
                    'tax_postcode' => array(
                        'value' => '95131'
                    ),
                    'tax_region_id' => array(
                        'value' => '12' // California
                    )
                )
            )
        );
    }

    /**
     * Rate US NY with 8.375%
     *
     * @return array
     */
    protected function _getRateUSNY()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'rate' => array(
                        'value' => '8.375'
                    ),
                    'tax_region_id' => array(
                        'value' => '43' // New York
                    )
                )
            )
        );
    }

    /**
     * Rate US NY with 8.1%
     *
     * @return array
     */
    protected function _getRateUSNYCustom()
    {
        return array(
            'data' => array(
                'fields' => array(
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
            )
        );
    }

    /**
     * Get UK full tax rate
     *
     * @param array $defaultData
     * @return array
     */
    protected function getUKFullTaxRate($defaultData)
    {
        return array_replace_recursive($defaultData, array(
            'data' => array(
                'fields' => array(
                    'rate' => array(
                        'value' => 20
                    ),
                    'tax_country_id' => array(
                        'value' => 'GB',
                    ),
                ),
            ),
        ));
    }
}

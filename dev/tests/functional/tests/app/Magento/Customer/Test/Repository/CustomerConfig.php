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

namespace Magento\Customer\Test\Repository;

use Magento\Core\Test\Repository;

/**
 * Class Customer Config Repository
 *
 * @package Magento\Core\Test\Repository
 */
class CustomerConfig extends Repository\Config
{
    /**
     * Config repository constructor
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);
        $this->_data['customer_vat'] = $this->getCustomerVat();
    }

    /**
     * General store and country options settings
     *
     * @return array
     */
    public function getCustomerVat()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'customer' => array(
                        'section' => 'customer',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'create_account' => array(
                                'fields' => array(
                                    'auto_group_assign' => array(
                                        'value' => self::YES_VALUE,
                                    ),
                                    'tax_calculation_address_type' => array(
                                        'value' => 'billing',
                                    ),
                                    'viv_domestic_group' => array(
                                        'value' => '%valid_vat_id_domestic%',
                                    ),
                                    'viv_intra_union_group' => array(
                                        'value' => '%valid_vat_id_union%',
                                    ),
                                    'viv_invalid_group' => array(
                                        'value' => '%invalid_vat_id%',
                                    ),
                                    'viv_error_group' => array(
                                        'value' => '%validation_error%',
                                    ),
                                    'vat_frontend_visibility' => array(
                                        'value' => self::YES_VALUE,
                                    ),
                                ),
                            ),
                            'country' => array(
                                'fields' => array(
                                    'eu_countries' => array(
                                        'value' => array('FR', 'DE', 'GB'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}

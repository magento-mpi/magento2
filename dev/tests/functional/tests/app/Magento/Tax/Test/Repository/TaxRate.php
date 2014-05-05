<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class TaxRate
 *
 * @package Magento\Tax\Test\Repository
 */
class TaxRate extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['US-CA-*-Rate 1'] = [
            'tax_calculation_rate_id' => '1',
            'tax_country_id' => 'US',
            'tax_region_id' => '12',
            'tax_postcode' => '*',
            'code' => 'US-CA-*-Rate 1',
            'rate' => '8.2500',
            'zip_is_range' => '',
            'zip_from' => '',
            'zip_to' => '',
            'id' => '1',
            'mtf_dataset_name' => 'US-CA-*-Rate 1',
        ];

        $this->_data['US-NY-*-Rate 1'] = [
            'tax_calculation_rate_id' => '2',
            'tax_country_id' => 'US',
            'tax_region_id' => '43',
            'tax_postcode' => '*',
            'code' => 'US-NY-*-Rate 1',
            'rate' => '8.3750',
            'zip_is_range' => '',
            'zip_from' => '',
            'zip_to' => '',
            'id' => '2',
            'mtf_dataset_name' => 'US-NY-*-Rate 1',
        ];

        $this->_data['us_ca_rate_8_25'] = [
            'code' => 'Tax Rate %isolation%',
            'rate' => '8.25',
            'tax_country_id' => 'United States',
            'tax_postcode' => '90230',
            'tax_region_id' => 'California',
        ];

        $this->_data['us_ny_rate_8_375'] = [
            'code' => 'Tax Rate %isolation%',
            'rate' => '8.375',
            'tax_country_id' => 'United States',
            'tax_region_id' => 'New York',
        ];

        $this->_data['us_ny_rate_8_1'] = [
            'code' => 'US-NY-*-%isolation%',
            'rate' => '8.1',
            'tax_country_id' => 'United States',
            'tax_region_id' => 'New York',
        ];

        $this->_data['paypal_rate_8_25'] = [
            'code' => 'Tax Rate %isolation%',
            'rate' => '8.25',
            'tax_country_id' => 'United States',
            'tax_postcode' => '95131',
            'tax_region_id' => 'California',
        ];

        $this->_data['uk_full_tax_rate'] = [
            'code' => 'Tax Rate %isolation%',
            'rate' => '10',
            'tax_country_id' => 'United Kingdom',
            'tax_postcode' => '*',
        ];

        $this->_data['default'] = [
            'code' => 'TaxIdentifier%isolation%',
            'tax_postcode' => '*',
            'tax_country_id' => 'Australia',
            'rate' => '20'
        ];

        $this->_data['withZipRange'] = [
            'code' => 'TaxIdentifier%isolation%',
            'zip_is_range' => 'yes',
            'zip_from' => '90001',
            'zip_to' => '96162',
            'tax_country_id' => 'United States',
            'tax_region_id' => 'California',
            'rate' => '15.5'
        ];
    }
}

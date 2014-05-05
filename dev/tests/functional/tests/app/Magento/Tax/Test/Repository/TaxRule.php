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
 * Class TaxRule
 *
 * @package Magento\Tax\Test\Repository
 */
class TaxRule extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['custom_rule'] = [
            'code' => 'TaxIdentifier%isolation%',
            'tax_rate' => [
                'dataSet' => [
                    0 => 'us_ca_rate_8_25',
                    1 => 'us_ny_rate_8_375',
                ]
            ],
        ];

        $this->_data['us_ca_ny_rule'] = [
            'code' => 'Tax Rule %isolation%',
            'tax_rate' => [
                'dataSet' => [
                    0 => 'US-CA-*-Rate 1',
                    1 => 'us_ny_rate_8_1',
                ],
            ],
            'tax_customer_class' => [
                0 => 'Retail Customer',
                1 => 'Customer Tax Class %isolation%',
            ],
            'tax_product_class' => [
                0 => 'Taxable Goods',
                1 => 'Product Tax Class %isolation%',
            ],
            'priority' => '0',
            'position' => '0',
        ];

        $this->_data['uk_full_tax_rule'] = [
            'code' => 'TaxIdentifier%isolation%',
            'tax_rate' => [
                'dataSet' => [
                    0 => 'uk_full_tax_rate',
                ],
            ],
        ];

        $this->_data['tax_rule_default'] = [
            'code' => 'TaxRule1',
            'tax_rate' => [
                'dataSet' => [
                    0 => 'US-CA-*-Rate 1'
                ],
            ],
            'tax_customer_class' => [
                0 => 'Retail Customer',
            ],
            'tax_product_class' => [
                0 => 'Taxable Goods'
            ],
            'priority' => '1',
            'position' => '1',

        ];

        $this->_data['tax_rule_with_custom_tax_classes'] = [
            'code' => 'TaxRule2',
            'tax_rate' => [
                'dataSet' => [
                    0 => 'US-CA-*-Rate 1',
                    1 => 'US-NY-*-Rate 1',
                ],
            ],
            'tax_customer_class' => [
                0 => 'Retail Customer',
                1 => 'CustomerTaxClass1',
            ],
            'tax_product_class' => [
                0 => 'Taxable Goods',
                1 => 'ProductTaxClass1',
            ],
            'priority' => '1',
            'position' => '1',
        ];
    }
}

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
 * Class TaxClass
 *
 * @package Magento\Tax\Test\Repository
 */
class TaxClass extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['taxable_goods'] = [
            'class_id' => '2',
            'class_name' => 'Taxable Goods',
            'class_type' => 'PRODUCT',
            'id' => '2',
            'mtf_dataset_name' => 'taxable_goods',
        ];

        $this->_data['retail_customer'] = [
            'class_id' => '3',
            'class_name' => 'Retail Customer',
            'class_type' => 'CUSTOMER',
            'id' => '3',
            'mtf_dataset_name' => 'retail_customer',
        ];

        $this->_data['tax_customer_class'] = [
            'class_name' => 'TaxClass%isolation%',
            'class_type' => 'CUSTOMER',
            'mtf_dataset_name' => 'tax_customer_class',
        ];

    }
}

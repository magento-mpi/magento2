<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CustomerGroup
 *
 * @package Magento\Customer\Test\Repository
 */
class CustomerGroup extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data[''] = [
            'customer_group_id' => '0',
            'customer_group_code' => 'NOT LOGGED IN',
            'tax_class_id' => '3',
            'id' => '0',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '1',
            'customer_group_code' => 'General',
            'tax_class_id' => '3',
            'id' => '1',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '2',
            'customer_group_code' => 'Wholesale',
            'tax_class_id' => '3',
            'id' => '2',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '3',
            'customer_group_code' => 'Retailer1',
            'tax_class_id' => '3',
            'id' => '3',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '4',
            'customer_group_code' => 'wer',
            'tax_class_id' => '3',
            'id' => '4',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '5',
            'customer_group_code' => 'sagres',
            'tax_class_id' => '3',
            'id' => '5',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '6',
            'customer_group_code' => '123',
            'tax_class_id' => '3',
            'id' => '6',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '7',
            'customer_group_code' => '1234',
            'tax_class_id' => '3',
            'id' => '7',
            'mtf_dataset_name' => '',
        ];

        $this->_data[''] = [
            'customer_group_id' => '8',
            'customer_group_code' => '3',
            'tax_class_id' => '3',
            'id' => '8',
            'mtf_dataset_name' => '',
        ];

    }
}

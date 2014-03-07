<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductBundle
 *
 * @package Magento\Bundle\Test\Repository
 */
class CatalogProductBundle extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['BundleDynamic_sku_1073507449'] = [
            'sku' => 'BundleDynamic_sku_10735074493',
            'name' => 'BundleDynamic 1073507449',
            'price' => [
                'price_from' => 1,
                'price_to' => 2
            ],
            'short_description' => '',
            'description' => '',
            'tax_class_id' => '2',
            'sku_type' => '0',
            'price_type' => '0',
            'weight_type' => '0',
            'status' => '1',
            'shipment_type' => '1',
            'mtf_dataset_name' => 'BundleDynamic_sku_1073507449'
        ];

        $this->_data['BundleDynamic_sku_215249172'] = [
            'sku' => 'BundleDynamic_sku_215249172',
            'name' => 'BundleDynamic 215249172',
            'price' => [
                'price_from' => 3,
                'price_to' => 4
            ],
            'short_description' => '',
            'description' => '',
            'tax_class_id' => '2',
            'sku_type' => '0',
            'weight_type' => '0',
            'price_type' => '0',
            'shipment_type' => '1',
            'mtf_dataset_name' => 'BundleDynamic_sku_215249172'
        ];
    }
}

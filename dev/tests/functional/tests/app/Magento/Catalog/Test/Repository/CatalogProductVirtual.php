<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogVirtualProduct
 * Virtual product for precondition
 */
class CatalogProductVirtual extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['virtual_product'] = [
            'sku' => '50_dollar_product%isolation%',
            'name' => '50_dollar_product%isolation%',
            'type_id' => 'virtual',
            'attribute_set_id' => '4',
            'price' => ['value' => 50, 'preset' => '-'],
            'id' => '3',
            'mtf_dataset_name' => '50_dollar_product'
        ];

        $this->_data['virtual_product2'] = [
            'sku' => '50_dollar_product%isolation%',
            'name' => '50_dollar_product%isolation%',
            'type_id' => 'virtual',
            'attribute_set_id' => '4',
            'price' => ['value' => 50, 'preset' => '-'],
            'id' => '3',
            'mtf_dataset_name' => '50_dollar_product'
        ];

        $this->_data['50_dollar_product'] = [
            'name' => 'virtual_product',
            'sku' => 'virtual_product%isolation%',
            'price' => ['value' => 50, 'preset' => '-'],
            'tax_class_id' => 'Taxable Goods',
            'quantity_and_stock_status' => [
                'qty' => '1111',
                'is_in_stock' => 'In Stock',
            ],
            'status' => 'Enabled',
            'website_ids' =>
                [
                    0 => 'Main Website',
                ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'qty' => '1111',
                'is_in_stock' => 'In Stock',
            ],
            'url_key' => 'virtual-product%isolation%',
            'visibility' => 'Catalog, Search',
        ];
    }
}

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
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'sku' => 'virtual_product%isolation%',
            'name' => 'virtual_product%isolation%',
            'type_id' => 'virtual',
            'attribute_set_id' => '4',
            'price' => ['value' => 100, 'preset' => '-'],
            'quantity_and_stock_status' => [
                'qty' => '1111',
                'is_in_stock' => 'In Stock',
            ],
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

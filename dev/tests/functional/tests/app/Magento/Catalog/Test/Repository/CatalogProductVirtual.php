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
 * Class CatalogProductVirtual
 * Data for creation Catalog Product Virtual
 */
class CatalogProductVirtual extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'status' => 'Product online',
            'website_ids' => ['Main Website'],
            'is_virtual' => 'Yes',
            'url_key' => 'virtual-product%isolation%',
            'visibility' => 'Catalog, Search',
            'attribute_set_id' => ['dataSet' => 'default'],
            'name' => 'Virtual product %isolation%',
            'sku' => 'sku_virtual_product_%isolation%',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'price' => ['value' => 10.00, 'preset' => '-']
        ];

        $this->_data['50_dollar_product'] = [
            'name' => 'virtual_product',
            'sku' => 'virtual_product%isolation%',
            'price' => ['value' => 50, 'preset' => '-'],
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
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

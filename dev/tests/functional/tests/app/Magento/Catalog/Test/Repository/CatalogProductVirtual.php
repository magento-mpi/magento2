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
            'attribute_set_id' => 'Default',
            'name' => 'Virtual product %isolation%',
            'sku' => 'sku_virtual_product_%isolation%',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'original_inventory_qty' => 666.0000,
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'price' => ['value' => 10.00, 'preset' => '-']
        ];
    }
}

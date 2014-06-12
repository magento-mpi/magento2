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
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'virtual_product%isolation%',
            'sku' => 'virtual_product%isolation%',
            'price' => ['value' => 10, 'preset' => '-'],
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'quantity_and_stock_status' => 'In Stock',
            'status' => 'Product online',
            'website_ids' => ['Main Website'],
            'qty' => 1111,
            'url_key' => 'virtual-product%isolation%',
            'visibility' => 'Catalog, Search',
            'attribute_set_id' => 'Default',
        ];
    }
}

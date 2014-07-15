<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductDownloadable
 * Data for creation Catalog Product Downloadable
 */
class CatalogProductDownloadable extends AbstractRepository
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
            'name' => 'Test downloadable product %isolation%',
            'sku' => 'sku_test_downloadable_product_%isolation%',
            'price' => ['value' => 280.00, 'preset' => '-'],
            'type_id' => 'downloadable',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'quantity_and_stock_status' => [
                'qty' => 90.0000,
                'is_in_stock' => 'In Stock',
            ],
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'url_key' => 'test-downloadable-product-%isolation%',
            'stock_data' => [
                'manage_stock' => 'Yes',
                'qty' => 90.0000,
                'is_in_stock' => 'Yes'
            ],
            'is_virtual' => 'Yes',
            'links_title' => 'Links',
            'links_purchased_separately' => 'Yes',
            'downloadable' => [
                'link' => [
                    [
                        'title' => 'Link title',
                        'price' => '1',
                        'number_of_downloads' => 1,
                        'is_shareable' => 'Use config',
                        'sample' => [
                            'type' => 'url',
                            'url' => 'http://example.com/',
                        ],
                        'type' => 'url',
                        'link_url' => 'http://example.com/',
                    ]
                ]
            ],
            'website_ids' => ['Main Website'],
        ];
    }
}

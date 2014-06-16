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
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['customDefault'] = [
            'name' => 'Test downloadable product %isolation%',
            'sku' => 'sku_test_downloadable_product_%isolation%',
            'price' => 280.00,
            'type_id' => 'downloadable',
            'tax_class' => ['Taxable Goods'],
            'quantity_and_stock_status' => [
                'qty' => 90.0000,
                'is_in_stock' => 'In Stock',
            ],
            'status' => 1,
            'category_ids' => ['presets' => 'default_subcategory'],
            'visibility' => 'Catalog, Search',
            'url_key' => 'test-downloadable-product-%isolation%',
            'is_virtual' => 'Yes',
            'links_title' => 'Links',
            'links_purchased_separately' => 'Yes',
            'downloadable' => [
                'link' => [
                    [
                        'link_id' => '0',
                        'title' => 'Link title',
                        'price' => '1',
                        'number_of_downloads' => '1',
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

        $this->_data['downloadable_product'] = [
            'name' => 'downloadable_product',
            'sku' => 'downloadable_product%isolation%',
            'price' => '20',
            'tax_class_id' => ['Taxable Goods'],
            'quantity_and_stock_status' => [
                'qty' => '1111',
                'is_in_stock' => 'In Stock',
            ],
            'status' => '1',
            'website_ids' => ['Main Website'],
            'url_key' => 'downloadable-product%isolation%',
            'visibility' => 'Catalog, Search',
            'samples_title' => 'Samples',
            'links_title' => 'Links',
            'links_purchased_separately' => 'Yes',
            'affect_configurable_product_attributes' => '1',
            'downloadable' => [
                'link' => [
                    [
                        'link_id' => '0',
                        'title' => 'Link title',
                        'price' => '1',
                        'number_of_downloads' => '0',
                        'is_shareable' => 'Use config',
                        'sample' => [
                            'type' => 'url',
                            'url' => 'http://example.com/',
                        ],
                        'type' => 'url',
                        'link_url' => 'http://example.com/',
                    ],
                ],
            ],
        ];
    }
}

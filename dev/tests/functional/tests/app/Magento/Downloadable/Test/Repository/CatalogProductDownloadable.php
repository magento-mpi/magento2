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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Test downloadable product %isolation%',
            'sku' => 'sku_test_downloadable_product_%isolation%',
            'price' => 280.00,
            'type_id' => 'downloadable',
            'tax_class' => ['Taxable Goods'],
            'quantity_and_stock_status' => [
                'qty' => 90.0000,
                'is_in_stock' => 'In Stock',
            ],
            'status' => 'Product online',
            'category_ids' => ['presets' => 'default_subcategory'],
            'visibility' => 'Catalog, Search',
            'url_key' => 'test-downloadable-product-%isolation%',
            'is_virtual' => 'Yes',
            'links_title' => 'Links',
            'links_purchased_separately' => 'Yes',
            'downloadable' => [
                'link' => [
                    [
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
    }
}

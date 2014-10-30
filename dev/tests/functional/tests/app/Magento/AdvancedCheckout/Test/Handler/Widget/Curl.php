<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Handler\Widget;

/**
 * Curl handler for creating widgetInstance/frontendApp.
 */
class Curl extends \Magento\Widget\Test\Handler\Widget\Curl
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'theme_id' => [
            'Magento Blank' => 2,
        ],
        'code' => [
            'Order by SKU' => 'order_by_sku'
        ],
        'page_group' => [
            'All Pages' => 'all_pages',
            'Specified Page' => 'pages',
            'Page Layouts' => 'page_layouts'
        ],
        'block' => [
            'Sidebar Additional' => 'sidebar.additional',
            'Sidebar Main' => 'sidebar.main'
        ]
    ];
}

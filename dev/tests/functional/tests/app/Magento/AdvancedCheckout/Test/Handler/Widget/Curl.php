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
            'Magento Blank' => 3,
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

    /**
     * Prepare Widget Instance data.
     *
     * @param array $data
     * @return array
     */
    protected function prepareWidgetInstance($data)
    {
        foreach ($data['widget_instance'] as $key => $widgetInstance) {
            $pageGroup = $widgetInstance['page_group'];
            if ($pageGroup === 'all_pages') {
                $widgetInstance[$pageGroup]['layout_handle'] = 'default';
                $widgetInstance[$pageGroup]['for'] = 'all';
                $widgetInstance[$pageGroup]['template'] = 'widget/sku.phtml';
                if (!isset($widgetInstance[$pageGroup]['page_id'])) {
                    $widgetInstance[$pageGroup]['page_id'] = 0;
                }
            }
            $data['widget_instance'][$key] = $widgetInstance;
        }

        return $data;
    }
}

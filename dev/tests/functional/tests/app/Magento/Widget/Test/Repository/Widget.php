<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Widget Repository
 */
class Widget extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $this->getFrontEndAppData());
    }

    /**
     * Data for Front End App Type
     */
    protected function getFrontEndAppData()
    {
        return array(
            'fields' => array(
                // Title
                'title' => array(
                    'value' => 'Test Frontend App'
                ),
                // All Store Views
                'store_ids' => array(
                    'value' => array(
                        '0' => '0'
                    )
                ),
                // Layout Updates
                'widget_instance' => array(
                    'value' => array(
                        '0' => array(
                            // Display On = All Pages
                            'page_group' => 'all_pages',
                            'all_pages' => array(
                                'page_id' => '0',
                                'layout_handle' => 'default',
                                'for' => 'all',
                                // Container = Main Content Area
                                'block' => 'content',
                                'template' => 'widget/block.phtml'
                            )
                        )
                    )
                ),
                // Catalog Promotions Related
                'parameters' => array(
                    'value' => array(
                        'display_mode' => 'catalogrule'
                    )
                )
            ),
            'theme' => '2'
        );
    }
}

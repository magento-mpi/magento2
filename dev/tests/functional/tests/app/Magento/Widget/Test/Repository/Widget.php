<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Widget Repository
 *
 * @package Magento\Widget\Test\Repository
 */
class Widget extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
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
            'theme' => '3'
        );
    }
}

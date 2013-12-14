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
 * Class widget Instance Repository
 *
 * @package Magento\Widget\Test\Repository
 */
class Widget extends AbstractRepository
{
    /**
     * Key for banner rotator frontend app instance type
     */
    const BANNER_ROTATOR = 'banner_rotator';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $defaultData);

        $this->_data[self::BANNER_ROTATOR] = $this->getBannerRotatorFrontEndApp();
    }

    /**
     * Data for Banner Rotator Front End App Type
     */
    protected function getBannerRotatorFrontEndApp()
    {
        return array(
            'data' => array(
                'fields' => array(
                    // Title
                    'title' => array(
                        'value' => 'Test Banner Rotator'
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
                                    // Container = Main Content Area
                                    'block' => 'content'
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
                'type' => 'magento_banner',
                'theme' => '3'
            )
        );
    }
}

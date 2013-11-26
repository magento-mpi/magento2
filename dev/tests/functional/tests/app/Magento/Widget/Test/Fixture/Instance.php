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

namespace Magento\Widget\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Instance
 *
 * @package Magento\Widget\Test\Fixture
 */

class Instance extends DataFixture
{
    /**
     * Create Widget Instance
     *
     * @return Instance
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoWidgetCreateInstance($this);
        $this->_data['fields']['id'] = $id;
        return $this;
    }


    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_data = array(
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
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoWidgetInstance($this->_dataConfig, $this->_data);
    }
}

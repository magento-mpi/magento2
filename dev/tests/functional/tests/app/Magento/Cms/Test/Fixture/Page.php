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

namespace Magento\Cms\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Page
 * CMS page
 *
 * @package Magento\Cms\Test\Fixture
 */
class Page extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_defaultConfig = array(
            'block_form_class' => '\\Magento\\Cms\\Test\\Block\\Backend\\PageForm',
            'block_grid_class' => '\\Magento\\Cms\\Test\\Block\\Backend\\PageGrid',

            'grid_filter'      => array('page_title'),

            'url_create_page'  => 'admin/cms_page/new',
            'url_update_page'  => 'admin/cms_page/edit',
            'url_grid_page'    => 'admin/cms_page/index'
        );

        $this->_repository = array(
            'cms_page' => array(
                'config' => array(
                    'constraint'       => 'Success'
                ),

                'data' => array(
                    'fields' => array(
                        'page_title'     => array(
                            'value' => 'CMS Page%isolation%',
                            'group' => 'page_tabs_main_section'
                        ),
                        'page_identifier' => array(
                            'value' => 'identifier%isolation%',
                            'group' => 'page_tabs_main_section'
                        ),
                        'page_content_heading'  => array(
                            'value' => 'CMS Page Head%isolation%',
                            'group' => 'page_tabs_content_section'
                        )
                    )
                )
            )
        );

        //Default data set
        $this->switchData('cms_page');
    }
}

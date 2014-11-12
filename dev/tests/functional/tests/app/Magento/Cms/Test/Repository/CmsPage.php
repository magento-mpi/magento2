<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CmsPage
 * Cms page repository
 */
class CmsPage extends AbstractRepository
{
    /**
     * @construct
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'title' => 'test-%isolation%',
            'identifier' => 'test-%isolation%',
            'store_id' => 'All Store Views',
            'is_active' => 'Published',
            'under_version_control' => 'No',
            'content' => [
                'content' => 'text content'
            ],
            'content_heading' => 'Test-%isolation%',
            'page_layout' => '1 column'
        ];

        $this->_data['cms-page-duplicated'] = [
            'store_id' => 'All Store Views',
            'title' => '404 Not Found 1 Test%isolation%',
            'meta_keywords' => 'Page keywords',
            'meta_description' => 'Page description',
            'identifier' => 'home',
            'content' => [
                'content' => 'Test Content'
            ],
            'is_active' => 'Published',
            'under_version_control' => 'No',
            'mtf_dataset_name' => 'cms-page-duplicated',
            'constraint' => 'cmsPageDuplicateError'
        ];

        $this->_data['cms-page-test'] = [
            'store_id' => 'All Store Views',
            'title' => 'CMS Page Test%isolation%',
            'content_heading' => 'CMS Page Test%isolation%',
            'meta_keywords' => 'Meta,Keys',
            'meta_description' => 'Meta Description',
            'identifier' => 'cms-page-test%isolation%',
            'content' => [
                'content' => 'Test Content'
            ],
            'is_active' => 'Published',
            'under_version_control' => 'Yes',
            'mtf_dataset_name' => 'cms-page-test',
            'constraint' => 'cmsPageSaveSuccess'
        ];

        $this->_data['3_column_template'] = [
            'title' => 'compare-%isolation%',
            'identifier' => 'compare-%isolation%',
            'store_id' => 'All Store Views',
            'is_active' => 'Published',
            'under_version_control' => 'No',
            'content' => [
                'content' => 'Test Content'
            ],
            'page_layout' => '3 columns'
        ];
    }
}

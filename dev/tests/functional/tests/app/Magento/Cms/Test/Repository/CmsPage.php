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
 *
 * @package Magento\Cms\Test\Repository
 */
class CmsPage extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['cms-page-duplicated'] = [
            'store_id' => 'All Store Views',
            'title' => '404 Not Found 1 Test%isolation%',
            'meta_keywords' => 'Page keywords',
            'meta_description' => 'Page description',
            'identifier' => 'home',
            'content' => 'Test Content',
            'is_active' => 'Published',
            'under_version_control' => 'No',
            'mtf_dataset_name' => 'cms-page-duplicated',
            'constraint' => 'cmsPageDuplicateError'
        ];

        $this->_data['cms-page-test'] = [
            'store_id' => 'All Store Views',
            'title' => 'CMS Page Test%isolation%',
            'meta_keywords' => 'Meta,Keys',
            'meta_description' => 'Meta Description',
            'identifier' => 'cms-page-test%isolation%',
            'content' => 'Test Content',
            'is_active' => 'Published',
            'under_version_control' => 'Yes',
            'mtf_dataset_name' => 'cms-page-test',
            'constraint' => 'cmsPageSaveSuccess'
        ];
    }
}

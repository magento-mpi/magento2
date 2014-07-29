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
 * Class UrlRewrite
 * Data for creation url rewrite
 */
class UrlRewrite extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default_no_redirect'] = [
            'store_id' => 'Default Store View',
            'request_path' => 'test_request%isolation%',
            'options' => 'No',
            'description' => 'test description',
            'id_path' => ['entity' => "cms_page/%cmsPage::default%"]
        ];

        $this->_data['default_temporary_redirect'] = [
            'store_id' => 'Default Store View',
            'request_path' => 'test_request%isolation%',
            'options' => 'Temporary (302)',
            'description' => 'test description',
            'id_path' => ['entity' => "cms_page/%cmsPage::default%"]
        ];

        $this->_data['default_permanent_redirect'] = [
            'store_id' => 'Default Store View',
            'request_path' => 'test_request%isolation%',
            'options' => 'Permanent (301)',
            'description' => 'test description',
            'id_path' => ['entity' => "cms_page/%cmsPage::default%"]
        ];
    }
}

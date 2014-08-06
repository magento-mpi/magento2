<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Page\Adminhtml;

use Magento\Cms\Test\Page\Adminhtml\CmsNew as ParentCmsNew;

/**
 * Class CmsNew
 * CMS New page on backend
 */
class CmsNew extends ParentCmsNew
{
    const MCA = 'admin/cms_page/new/index';

    protected $_blocks = [
        'pageForm' => [
            'name' => 'pageForm',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm
     */
    public function getPageForm()
    {
        return $this->getBlockInstance('pageForm');
    }
}

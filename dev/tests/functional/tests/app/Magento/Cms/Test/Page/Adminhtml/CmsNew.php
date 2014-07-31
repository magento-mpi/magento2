<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CmsNew
 * CMS New page on backend
 */
class CmsNew extends BackendPage
{
    const MCA = 'admin/cms_page/new';

    protected $_blocks = [
        'pageForm' => [
            'name' => 'pageForm',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Page\Edit\PageForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'name' => 'pageMainActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'versionsGrid' => [
            'name' => 'versionsGrid',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab\VersionsGrid',
            'locator' => '#bannerGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Page\Edit\PageForm
     */
    public function getPageForm()
    {
        return $this->getBlockInstance('pageForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab\VersionsGrid
     */
    public function getVersionsGrid()
    {
        return $this->getBlockInstance('versionsGrid');
    }
}

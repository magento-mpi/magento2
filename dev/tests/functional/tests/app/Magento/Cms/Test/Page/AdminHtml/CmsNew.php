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
 * Page for create CMS Page
 */
class CmsNew extends BackendPage
{
    const MCA = 'admin/cms_page/new';

    protected $_blocks = [
        'newCmsPageForm' => [
            'name' => 'newCmsPageForm',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Page\PageForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'name' => 'pageMainActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Page\PageForm
     */
    public function getNewCmsPageForm()
    {
        return $this->getBlockInstance('newCmsPageForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }
}

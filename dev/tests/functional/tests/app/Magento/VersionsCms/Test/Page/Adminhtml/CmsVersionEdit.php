<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CmsVersionEdit
 * Cms Page Version Edit on backend
 */
class CmsVersionEdit extends BackendPage
{
    const MCA = 'admin/cms_page_version/edit';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'versionForm' => [
            'name' => 'versionForm',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\Edit\VersionForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\Edit\VersionForm
     */
    public function getVersionForm()
    {
        return $this->getBlockInstance('versionForm');
    }
}

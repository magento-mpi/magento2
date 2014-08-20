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
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'versionForm' => [
            'name' => 'versionForm',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\Edit\VersionForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'revisionsGrid' => [
            'name' => 'revisionsGrid',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\Edit\RevisionsGrid',
            'locator' => '#revisionsGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\FormPageActions
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

    /**
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\Edit\RevisionsGrid
     */
    public function getRevisionsGrid()
    {
        return $this->getBlockInstance('revisionsGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

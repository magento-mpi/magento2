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
 */
class CmsNew extends BackendPage
{
    const MCA = 'admin/cms_page/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageForm' => [
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Page\Edit\PageForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'pageVersionsForm' => [
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
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
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm
     */
    public function getPageVersionsForm()
    {
        return $this->getBlockInstance('pageVersionsForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

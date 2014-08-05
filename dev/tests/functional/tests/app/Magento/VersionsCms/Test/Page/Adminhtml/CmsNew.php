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
 * Class CmsNew
 * Cms Page Edit on backend
 */
class CmsNew extends BackendPage
{
    const MCA = 'admin/cms_page/new/index';

    protected $_blocks = [
        'pageForm' => [
            'name' => 'pageForm',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm',
            'locator' => '[id="page:main-container"]',
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
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm
     */
    public function getPageForm()
    {
        return $this->getBlockInstance('pageForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

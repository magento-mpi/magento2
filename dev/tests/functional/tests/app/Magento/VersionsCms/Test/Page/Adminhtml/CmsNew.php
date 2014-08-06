<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Page\Adminhtml;

/**
 * Class CmsNew
 * Cms Page Edit on backend
 */
class CmsNew extends \Magento\Cms\Test\Page\Adminhtml\CmsNew
{
    const MCA = 'admin/cms_page/new/index';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['pageForm'] = [
            'name' => 'pageForm',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit\PageForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ];
        $this->_blocks['messagesBlock'] = [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ];
    }

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

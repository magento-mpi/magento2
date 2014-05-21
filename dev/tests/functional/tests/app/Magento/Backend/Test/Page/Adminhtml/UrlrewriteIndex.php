<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class UrlRewriteIndex
 */
class UrlrewriteIndex extends BackendPage
{
    const MCA = 'admin/urlrewrite/index';

    protected $_blocks = [
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'urlRedirectGrid' => [
            'name' => 'urlRedirectGrid',
            'class' => 'Magento\Backend\Test\Block\Urlrewrite\Catalog\Category\Grid',
            'locator' => '#urlrewriteGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Category\Grid
     */
    public function getUrlRedirectGrid()
    {
        return $this->getBlockInstance('urlRedirectGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

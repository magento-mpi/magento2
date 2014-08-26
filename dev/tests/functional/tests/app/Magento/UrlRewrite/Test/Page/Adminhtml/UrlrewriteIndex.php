<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class UrlrewriteIndex
 */
class UrlrewriteIndex extends BackendPage
{
    const MCA = 'admin/urlrewrite/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'urlRedirectGrid' => [
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category\Grid',
            'locator' => '#urlrewriteGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
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
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category\Grid
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

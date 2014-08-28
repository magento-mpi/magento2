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
 * Class UrlRewriteIndex
 */
class UrlRewriteIndex extends BackendPage
{
    const MCA = 'admin/url_rewrite/index';

    protected $_blocks = [
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'urlRewriteGrid' => [
            'name' => 'urlRewriteGrid',
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category\Grid',
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
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category\Grid
     */
    public function getUrlRewriteGrid()
    {
        return $this->getBlockInstance('urlRewriteGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class SitemapIndex
 */
class SitemapIndex extends BackendPage
{
    const MCA = 'admin/sitemap/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'gridPageActions' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'sitemapGrid' => [
            'class' => 'Magento\Sitemap\Test\Block\Adminhtml\SitemapGrid',
            'locator' => '#sitemapGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Sitemap\Test\Block\Adminhtml\SitemapGrid
     */
    public function getSitemapGrid()
    {
        return $this->getBlockInstance('sitemapGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

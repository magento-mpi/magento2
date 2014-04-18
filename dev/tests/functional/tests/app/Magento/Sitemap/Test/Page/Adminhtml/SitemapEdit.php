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
 * Class SitemapEdit
 *
 * @package Magento\Sitemap\Test\Page\Adminhtml
 */
class SitemapEdit extends BackendPage
{
    const MCA = 'admin/sitemap/edit';

    protected $_blocks = [
        'sitemapPageActions' => [
            'name' => 'sitemapPageActions',
            'class' => 'Magento\Sitemap\Test\Block\Adminhtml\SitemapPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Sitemap\Test\Block\Adminhtml\SitemapPageActions
     */
    public function getSitemapPageActions()
    {
        return $this->getBlockInstance('sitemapPageActions');
    }
}

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
 * Class SitemapNew
 */
class SitemapNew extends BackendPage
{
    const MCA = 'sitemap/new/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'sitemapForm' => [
            'class' => 'Magento\Backend\Test\Block\Widget\Form',
            'locator' => '#add_sitemap_form',
            'strategy' => 'css selector',
        ],
        'sitemapPageActions' => [
            'class' => 'Magento\Sitemap\Test\Block\Adminhtml\SitemapPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\Widget\Form
     */
    public function getSitemapForm()
    {
        return $this->getBlockInstance('sitemapForm');
    }

    /**
     * @return \Magento\Sitemap\Test\Block\Adminhtml\SitemapPageActions
     */
    public function getSitemapPageActions()
    {
        return $this->getBlockInstance('sitemapPageActions');
    }
}

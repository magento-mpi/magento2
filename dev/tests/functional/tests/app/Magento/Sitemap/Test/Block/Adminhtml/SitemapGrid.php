<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class SitemapGrid
 * Backend sitemap grid
 *
 */
class SitemapGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'sitemap_filename' => [
            'selector' => '#sitemapGrid_filter_sitemap_filename',
        ],
        'sitemap_path' => [
            'selector' => '#sitemapGrid_filter_sitemap_path'
        ],
        'sitemap_id' => [
            'selector' => '#sitemapGrid_filter_sitemap_id'
        ]
    ];

    /**
     * Locator value for link in sitemap id column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-sitemap_id]';

    /**
     * Locator link for Google in grid
     *
     * @var string
     */
    protected $linkForGoogle = 'tbody tr .col-link a';

    /**
     * Locator sitemap id in grid
     *
     * @var string
     */
    protected $sitemapId = 'tbody tr .col-sitemap_id';

    /**
     * Locator generate link
     *
     * @var string
     */
    protected $generate = 'tbody tr .col-action a';

    /**
     * Get link for Google
     *
     * @return string
     */
    public function getLinkForGoogle()
    {
        return $this->_rootElement->find($this->linkForGoogle, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get sitemap id
     *
     * @return string
     */
    public function getSitemapId()
    {
        return $this->_rootElement->find($this->sitemapId, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Generate sitemap
     *
     * @return void
     */
    public function generate()
    {
        $this->_rootElement->find($this->generate, Locator::SELECTOR_CSS)->click();
    }
}

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
class UrlRewriteIndex extends BackendPage
{
    const MCA = 'admin/urlrewrite/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
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
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Category\Grid
     */
    public function getUrlRedirectGrid()
    {
        return $this->getBlockInstance('urlRedirectGrid');
    }
}

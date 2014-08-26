<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class SearchIndex
 */
class SearchIndex extends BackendPage
{
    const MCA = 'reports/index/search';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'searchGrid' => [
            'class' => 'Magento\Reports\Test\Block\Adminhtml\SearchTermsGrid',
            'locator' => '#searchReportGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Reports\Test\Block\Adminhtml\SearchTermsGrid
     */
    public function getSearchGrid()
    {
        return $this->getBlockInstance('searchGrid');
    }
}

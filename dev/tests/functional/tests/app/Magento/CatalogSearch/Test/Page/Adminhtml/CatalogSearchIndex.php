<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogSearchIndex
 */
class CatalogSearchIndex extends BackendPage
{
    const MCA = 'catalog/search/index';

    protected $_blocks = [
        'grid' => [
            'name' => 'grid',
            'class' => 'Magento\CatalogSearch\Test\Block\Adminhtml\Grid',
            'locator' => '#catalog_search_grid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CatalogSearch\Test\Block\Adminhtml\Grid
     */
    public function getGrid()
    {
        return $this->getBlockInstance('grid');
    }
}

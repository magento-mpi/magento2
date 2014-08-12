<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page\Category;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView as AbstractCatalogCategoryView;

/**
 * Class CatalogCategoryView
 * Catalog Category page
 */
class CatalogCategoryView extends AbstractCatalogCategoryView
{
    const MCA = 'banner/catalog/category/view';

    protected $_blocks = [
        'viewBlock' => [
            'name' => 'viewBlock',
            'class' => 'Magento\Banner\Test\Block\Category\View',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Banner\Test\Block\Category\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }
}

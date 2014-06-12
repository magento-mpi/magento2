<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class CatalogCategoryIndex
 * Category page on the Backend
 */
class CatalogCategoryIndex extends BackendPage
{
    const MCA = 'catalog/category/index/index'; // TODO: Fix after resolving issue with factory page generation

    protected $_blocks = [
        'treeCategories' => [
            'name' => 'treeCategories',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Category\Tree',
            'locator' => '[id="page:left"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Tree
     */
    public function getTreeCategories()
    {
        return $this->getBlockInstance('treeCategories');
    }
}

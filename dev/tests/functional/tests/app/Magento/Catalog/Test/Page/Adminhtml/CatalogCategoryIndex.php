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
 * Category page
 */
class CatalogCategoryIndex extends BackendPage
{
    const MCA = 'catalog/category/index';

    protected $_blocks = [
        'treeCategories' => [
            'name' => 'treeCategories',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Category\Tree',
            'locator' => '[id="page:left"]',
            'strategy' => 'css selector',
        ]
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Tree
     */
    public function getTreeCategories()
    {
        return $this->getBlockInstance('treeCategories');
    }
}

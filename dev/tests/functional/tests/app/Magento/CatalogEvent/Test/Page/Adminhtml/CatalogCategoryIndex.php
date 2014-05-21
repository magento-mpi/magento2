<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;
use Magento\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex as AbstractCatalogCategoryIndex;

/**
 * Class CatalogCategoryIndex
 * Category page
 */
class CatalogCategoryIndex extends AbstractCatalogCategoryIndex
{
    const MCA = 'catalog/category/index';

    protected $_blocks = [
        'treeCategories' => [
            'name' => 'treeCategories',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Category\Tree',
            'locator' => '[id="page:left"]',
            'strategy' => 'css selector',
        ],
        'pageActionsEvent' => [
            'name' => 'pageActionsEvent',
            'class' => 'Magento\CatalogEvent\Test\Block\Adminhtml\Category\FormPageActions',
            'locator' => '.page-actions',
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

    /**
     * @return \Magento\CatalogEvent\Test\Block\Adminhtml\Category\FormPageActions
     */
    public function getPageActionsEvent()
    {
        return $this->getBlockInstance('pageActionsEvent');
    }
}

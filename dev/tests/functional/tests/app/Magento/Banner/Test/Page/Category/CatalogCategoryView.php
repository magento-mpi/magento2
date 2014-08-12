<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page\Category;

/**
 * Class CatalogCategoryView
 * Catalog Category page
 */
class CatalogCategoryView extends \Magento\Catalog\Test\Page\Category\CatalogCategoryView
{
    const MCA = 'banner/catalog/category/view';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['viewBlock'] = [
            'name' => 'viewBlock',
            'class' => 'Magento\Banner\Test\Block\Category\View',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\Banner\Test\Block\Category\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category\Edit;

use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 * Category container block
 *
 */
class Form extends FormTabs
{
    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id=category-edit-form-save-button]';

    /**
     * Category Products grid
     *
     * @var string
     */
    protected $productsGridBlock = '#catalog_category_products';

    /**
     * Get Category edit form
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Tab\ProductGrid
     */
    public function getCategoryProductsGrid()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlCategoryTabProductGrid(
            $this->_rootElement->find($this->productsGridBlock)
        );
    }
}

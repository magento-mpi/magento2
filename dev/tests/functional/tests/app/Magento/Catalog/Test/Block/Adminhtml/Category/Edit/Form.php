<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category\Edit;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 * Category container block
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Category\Edit
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
     * Category products tab of selected category
     *
     * @var string
     */
    protected $categoryProductsTab = '#category_info_tabs_products';

    /**
     * Custom tab classes for product form
     *
     * @var array
     */
    protected $tabClasses = array(
        'category_info_tabs_group_4' => '\\Magento\\Catalog\\Test\\Block\\Adminhtml\\Category\\Tab\\Attributes'
    );

    /**
     * Category Products grid
     *
     * @var string
     */
    protected $productsGridBlock = '#catalog_category_products';

    public function getCategoryId()
    {
        $idField = $this->_rootElement->find('group_4id', Locator::SELECTOR_ID);
        return $idField->getValue();
    }

    /**
     * Open Category Products tab
     */
    public function openCategoryProductsTab()
    {
        $this->_rootElement->find($this->categoryProductsTab)->click();
    }

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

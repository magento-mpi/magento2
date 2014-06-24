<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class CategoryProducts
 * Products grid of Category Products tab
 */
class CategoryProducts extends Tab
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-in_category';

    /**
     * Fill category products
     *
     * @param array $fields
     * @param Element|null $element
     * @return void
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['products_name']['source']->getData() as $productName) {
            if ($productName != '-') {
                $this->getRoleGrid()->searchAndSelect(['name' => $productName]);
            }
        }
    }

    /**
     * Returns role grid
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Tab\ProductGrid
     */
    public function getRoleGrid()
    {
        return $this->blockFactory->create(
            'Magento\Catalog\Test\Block\Adminhtml\Category\Tab\ProductGrid',
            ['element' => $this->_rootElement->find('#catalog_category_products')]
        );
    }
}

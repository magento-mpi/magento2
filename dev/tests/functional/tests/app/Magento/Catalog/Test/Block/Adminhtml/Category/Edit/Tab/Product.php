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
use Magento\Catalog\Test\Block\Adminhtml\Category\Tab\ProductGrid;

/**
 * Class Product
 * Products grid of Category Products tab
 */
class Product extends Tab
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-in_category';

    /**
     * Path to the product grid tab
     *
     * @var string
     */
    protected $productGrid = 'Magento\Catalog\Test\Block\Adminhtml\Category\Tab\ProductGrid';

    /**
     * Fill category products
     *
     * @param array $fields
     * @param Element|null $element
     * @return void
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (!isset($fields['category_products'])) {
            return;
        }
        foreach ($fields['category_products']['source']->getData() as $productName) {
            $this->getProductGrid()->searchAndSelect(['name' => $productName]);
        }
    }

    /**
     * Returns role grid
     *
     * @return ProductGrid
     */
    public function getProductGrid()
    {
        return $this->blockFactory->create(
            $this->productGrid,
            ['element' => $this->_rootElement->find('#catalog_category_products')]
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\GridPageActions as ParentGridPageActions;
use Mtf\Client\Element\Locator;

/**
 * Class GridPageAction
 * Catalog manage products block
 */
class GridPageAction extends ParentGridPageActions
{
    /**
     * Product toggle button
     *
     * @var string
     */
    protected $toggleButton = '[data-ui-id="products-list-add-new-button-dropdown"]';

    /**
     * Product type item
     *
     * @var string
     */
    protected $productItem = '[data-ui-id="products-list-add-new-button-item-%productType%"]';

    /**
     * Add product using split button
     *
     * @param string $productType
     * @return void
     */
    public function addProduct($productType = 'simple')
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find(
            str_replace('%productType%', $productType, $this->productItem),
            Locator::SELECTOR_CSS
        )->click();
    }
}

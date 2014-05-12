<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Product
 * Catalog manage products block
 *
 */
class Product extends Block
{
    /**
     * Product toggle button
     *
     * @var string
     */
    protected $toggleButton = '[data-ui-id=products-list-add-new-button-dropdown]';

    /**
     * Product type item
     *
     * @var string
     */
    protected $productItem = '[data-ui-id=products-list-add-new-button-item-%productType%]';

    /**
     * Add product using split button
     *
     * @param string $productType
     */
    public function addProduct($productType = 'simple')
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find(str_replace('%productType%', $productType, $this->productItem),
            Locator::SELECTOR_CSS)->click();
    }
}

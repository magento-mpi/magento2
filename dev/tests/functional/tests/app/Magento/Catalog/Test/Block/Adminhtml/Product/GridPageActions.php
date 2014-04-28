<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\GridPageActions as AbstractGridPageActions;
use Mtf\Client\Element\Locator;

/**
 * Class GridPageActions
 * Grid page actions block
 *
 * @package Magento\Backend\Test\Block\Adminhtml\Product
 */
class GridPageActions extends AbstractGridPageActions
{
    /**
     * "Dropdown" button
     *
     * @var string
     */
    protected $toggleButton = "[data-ui-id=products-list-add-new-button-dropdown]";
    /**
     * "typeProducts" button
     *
     * @var string
     */
    protected $typeProducts;

    public function setTypeProduct($typeProducts)
    {
        $this->typeProducts = $typeProducts;
    }

    /**
     * Click on "Add New" button
     */
    public function addNew()
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find(
            "[data-ui-id=products-list-add-new-button-item-" . $this->typeProducts . "]",
            Locator::SELECTOR_CSS
        )->click();
    }
}
 
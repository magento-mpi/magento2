<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml\Create;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Adminhtml items to ship block
 */
class Items extends Block
{
    /**
     * Shipment submit button
     *
     * @var string
     */
    protected $submitShipment = '[data-ui-id="order-items-submit-button"]';

    /**
     * Product qty selector
     *
     * @var string
     */
    protected $productQty = '//tr[//*[contains(.,"%s")]]//input[contains(@class,"qty-item")]';

    /**
     * Click 'Submit Shipment' button
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitShipment)->click();
    }

    /**
     * Set product qty
     *
     * @param array $products
     * @param array $qty
     * @return void
     */
    public function setProductQty(array $products, array $qty)
    {
        foreach ($products as $key => $product) {
            $productQtySelector = sprintf($this->productQty, $product->getName());
            $this->_rootElement->find($productQtySelector, Locator::SELECTOR_XPATH)->setValue($qty[$key]);
        }
    }
}

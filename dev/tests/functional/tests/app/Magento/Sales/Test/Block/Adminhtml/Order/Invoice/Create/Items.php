<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Items\Product;

/**
 * Class Items
 * Block for items to invoice on new invoice page
 */
class Items extends Block
{
    /**
     * Invoice history css selector
     *
     * @var string
     */
    protected $comment = '[name="invoice[comment_text]"]';

    /**
     * Item product
     *
     * @var string
     */
    protected $productItems = '//tr[contains(.,"%s")]';

    /**
     * 'Update Qty's' button css selector
     *
     * @var string
     */
    protected $updateQty = '.update-button';

    /**
     * Set invoice history
     *
     * @param string $text
     * @return void
     */
    public function setHistory($text)
    {
        $this->_rootElement->find($this->comment)->setValue($text);
    }

    /**
     * Get item product block
     *
     * @param string $sku
     * @return Product
     */
    public function getItemProductBlockBySku($sku)
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Items\Product',
            ['element' => $this->_rootElement->find(sprintf($this->productItems, $sku), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Click update qty button
     *
     * @return void
     */
    public function clickUpdateQty()
    {
        $this->_rootElement->find($this->updateQty)->click();
    }
}

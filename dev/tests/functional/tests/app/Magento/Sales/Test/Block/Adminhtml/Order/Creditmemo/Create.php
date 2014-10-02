<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo;

use Mtf\Block\Block;
use Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo\Create\Items;

/**
 * Class Create
 * Credit memo create block
 */
class Create extends Block
{
    /**
     * Items block css selector
     *
     * @var string
     */
    protected $items = '#creditmemo_item_container';

    /**
     * Refund column selector
     *
     * @var string
     */
    protected $refundColumn = '.col-refund';

    /**
     * 'Refund Offline' button
     *
     * @var string
     */
    protected $refundOffline = '.submit-button';

    /**
     * 'Refund' button
     *
     * @var string
     */
    protected $refund = '.submit-button.refund';

    /**
     * Fill credit memo data
     *
     * @param array $data
     * @param array|null $products [optional]
     * @return void
     */
    public function fill(array $data, $products = null)
    {
        if (isset($data['comment'])) {
            $this->getItemsBlock()->setHistory($data['comment']);
        }
        if (isset($data['qty']) && $data['qty'] !== '-' && $products !== null) {
            foreach ($products as $product) {
                $this->getItemsBlock()->getItemProductBlockBySku($product->getSku())->setQty($data['qty']);
            }
            $this->getItemsBlock()->clickUpdateQty();
        }
    }

    /**
     * Get items block
     *
     * @return Items
     */
    protected function getItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo\Create\Items',
            ['element' => $this->_rootElement->find($this->items)]
        );
    }

    /**
     * Refund offline order
     *
     * @return void
     */
    public function refundOffline()
    {
        $browser = $this->_rootElement;
        $selector = $this->refundOffline . '.disabled';
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
        $this->reinitRootElement();
        $this->_rootElement->find($this->refundOffline)->click();
    }

    /**
     * Refund order
     *
     * @return void
     */
    public function refund()
    {
        $this->_rootElement->find($this->refund)->click();
    }
}
